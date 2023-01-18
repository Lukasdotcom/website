<?php
require_once "api.php";
/**
 * Used to move a card
 * @param string $user The username of the player.
 * @param string $game The game of the player.
 * @param string $swap1 Which card should be swapped.
 * @param string $swap2 If deck or discard should be swapped.
 * @return array Code gives a http response code that would be good to associate with the reponse ex: 200=success and 400=failure and text gives a more precise message.
 */
function moveCard($user, $game, $swap1, $swap2)
{
    $self = dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$game' and user='$user'");
    $gameData = dbRequest2("SELECT * FROM golfGame WHERE ID='$game'");
    if (!$self) {
        return array("code" => 404, "text" => "Player does not exist");
    }
    if (!$gameData) {
        return array("code" => 404, "text" => "Game does not exist");
    }
    $gameData = $gameData[0];
    $selfID = $self[0]["orderID"];
    $gameCurrentPlayer = $gameData["currentPlayer"];
    if ($gameCurrentPlayer != $selfID) { # Makes sure that it is the players turn
        return array("code" => 403, "text" => "It is not your turn");
    }
    $cardSwap = dbRequest2("SELECT * FROM golfGameCards WHERE user='$user' and gameID='$game' and cardPlacement='$swap1'");
    if (!(($swap2 == "discard" or $swap2 == "deck") and $cardSwap)) { # Makes sure that player gave valid cards to be switched
        return array("code" => 400, "text" => "Invalid Request");
    }
    // Makes sure the discard is reshuffled if neccessary.
    $gameData = reshuffleDeck($gameData);
    $deck = json_decode($gameData["deck"]);
    $discard = json_decode($gameData["discard"]);
    if ($swap2 == "discard" and $discard) { // Checks if the player wants to switch the discard pile or deck and if that is possible
        $newCard = array_pop($discard);
    } else {
        $newCard = array_pop($deck);
    }
    array_push($discard, $cardSwap[0]["card"]);
    dbCommand("UPDATE golfGameCards SET card=$newCard, faceUp=1 WHERE user='$user' and gameID='$game' and cardPlacement='$swap1'");
    $deck = json_encode($deck);
    $discard = json_encode($discard);
    do { // Will make sure the next picked player is a valid player that exists and is not eliminated.
        $gameCurrentPlayer++;
        if ($gameData["playersToStart"] + $gameData["bots"] <= $gameCurrentPlayer) {
            $gameCurrentPlayer = 0;
        }
    } while (dbRequest2("SELECT * FROM golfGamePlayers WHERE lastMode='eliminated' and orderID='$gameCurrentPlayer' and gameID='$game'"));
    $time = time();
    $timeLeft = $gameData["skipTime"];
    dbCommand("UPDATE golfGame SET deck='$deck', discard='$discard', currentPlayer='$gameCurrentPlayer', turnStartTime='$time', timeLeft=$timeLeft WHERE id=$game");
    dbCommand("UPDATE golfGamePlayers SET upToDate=0 WHERE gameID='$game'");
    return array("code" => 200, "text" => "Switched card #$swap1 with $swap2");
}
/**
 * Calculates the points for a certain player
 * @param string $user The username of the player.
 * @param string $game The game of the player.
 * @return int The amount of points the player has currently.
 */
function calculatePoints($user, $game)
{
    $cardValues = [1, -2, 3, 4, 5, 6, 7, 8, 9, 10, 10, 10, 0];
    $cardAmount = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    $points = 0;
    $cards = dbRequest2("SELECT card FROM golfGameCards WHERE gameID='$game' and user='$user' and faceUp");
    if ($cards) {
        foreach ($cards as $card) {
            $cardAmount[$card["card"] % 13]++;
        }
        foreach ($cardAmount as $card => $amount) {
            if ($amount == 1) {
                $points += $cardValues[$card];
            }
        }
    }
    // Used to check for the multiplier and multiply it to the points.
    $multiplier = dbRequest2("SELECT multiplier FROM golfGamePlayers WHERE gameID='$game' and user='$user'");
    if ($multiplier) {
        return intval($points * $multiplier[0]["multiplier"]);
    } else {
        return $points;
    }
}
/**
 * Is used to check if the deck  needs to be reshuffled and if it does it reshuffles the deck.
 * @param array $game The data for the game.
 */
function reshuffleDeck($game)
{
    $deck = json_decode($game["deck"]);
    if (!$deck) {
        $ID = $game["ID"];
        $discard = json_decode($game["discard"]);
        $newDiscard = array_pop($discard);
        shuffle($discard);
        $deck = json_encode($discard);
        $newDiscard = array(
            $newDiscard
        );
        $discard = json_encode($newDiscard);
        dbCommand("UPDATE golfGame SET deck='$deck', discard='$discard' WHERE ID='$ID'");
        $game = dbRequest2("SELECT * FROM golfGame WHERE ID='$ID'")[0];
    }
    return $game;
}
/**
 * Gets a round ready to start.
 * @param string $game The game of the player.
 */
function readyGame($game)
{
    $deck = array(); # Used to get the deck ready
    $decks = dbRequest2("SELECT decks FROM golfGame WHERE ID='$game'", "decks")[0];
    # Makes sure that it is not asking for an unreasonable amount of decks
    if ($decks > 50) {
        $decks = 50;
    } elseif ($decks < 1) {
        $decks = 1;
    }
    # Creates the cards for all the decks.
    for ($j = 0; $j < $decks; $j++) {
        for ($i = 0; $i < 52; $i++) {
            array_push($deck, $i);
        }
    }
    shuffle($deck);
    $gameData = dbRequest2("SELECT * FROM golfGame WHERE ID='$game'");
    if ($gameData) { # Makes sure the game exists
        $validPlayer = -1;
        $gameData = $gameData[0];
        $players = dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$game'");
        dbCommand("DELETE FROM golfGameCards WHERE gameID='$game'");
        $cards = $gameData["cardNumber"];
        $flippedCards = $gameData["flipNumber"];
        shuffle($players);
        $playerCount = count($players);
        for ($i = 0; $i < $playerCount; $i++) { # Goes through every player and gives them their cards and puts them in the correct order.
            $name = $players[$i]["user"];
            if ($players[$i]["points"] < $gameData["pointsToEnd"] and $players[$i]["turnsSkipped"] <= $gameData["skipTurns"]) { // Checks if the player is still in the game.
                $cardsToFlip = $flippedCards;
                for ($j = 1; $j <= $cards; $j++) {
                    $card = array_pop($deck);
                    if ($cardsToFlip > 0) {
                        dbCommand("INSERT INTO golfGameCards VALUES ('$game', '$name', '$card', '$j', '1')");
                        $cardsToFlip--;
                    } else {
                        dbCommand("INSERT INTO golfGameCards VALUES ('$game', '$name', '$card', '$j', '0')");
                    }
                }
                $mode = "";
                if ($validPlayer == -1) {
                    $validPlayer = $i;
                }
            } else {
                $mode = "eliminated";
            }
            dbCommand("UPDATE golfGamePlayers SET orderID='$i', multiplier=1, lastMode='$mode', upToDate=0 WHERE gameID='$game' and user='$name'");
        }
        writeLog(17, "New round started for game #$game");
        $json_deck = json_encode($deck); # Updates the deck and discard pile.
        $time = time();
        dbCommand("UPDATE golfGame SET currentPlayer=$validPlayer, deck='$json_deck', discard='[]', turnStartTime='$time' WHERE ID='$game'");
        return dbRequest2("SELECT * FROM golfGame WHERE ID='$game'")[0];
    }
}
if ($USERNAME) {
    if (array_key_exists("game", $_GET)) { // Gets the game
        # Finds all games the player is still playing.
        $playing = dbRequest2("SELECT name, password, players, playersToStart, bots, cardNumber, flipNumber, multiplierForFlip, pointsToEnd, ID, decks, skipTime, skipTurns, resetPoints FROM golfGame WHERE EXISTS (SELECT * FROM golfGamePlayers WHERE golfGamePlayers.gameID = ID and user='$USERNAME') ORDER BY turnStartTime DESC");
        # Finds all open games.
        $data = dbRequest2("SELECT name, password, players, playersToStart, bots, cardNumber, flipNumber, multiplierForFlip, pointsToEnd, ID, decks, skipTime, skipTurns, resetPoints FROM golfGame WHERE players != playersToStart and NOT EXISTS (SELECT * FROM golfGamePlayers WHERE golfGamePlayers.gameID = ID and user='$USERNAME') ORDER BY players DESC");
        foreach ($data as $id => $entry) { // Makes sure to not leak the password
            if ($entry["password"]) {
                $data[$id]["password"] = true;
            }
        }
        $data = array_merge($playing, $data);
        echo json_encode($data);
    } elseif (array_key_exists("update", $_GET)) {
        $id = $_GET["update"];
        if (dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and user='$USERNAME'")) { // WIll check if the player is playing the game
            $game = dbRequest2("SELECT * FROM golfGame WHERE ID='$id'");
            if ($game) { // Will check if the game exists
                $game = $game[0];
                while (dbRequest2("SELECT * FROM golfGame WHERE ID='$id' and locked")) { // Will wait until the game is not locked to make sure that only one person is looking at values at a time
                    sleep(0.05);
                }
                dbCommand("UPDATE golfGame SET locked=1 WHERE ID='$id'");
                if ($game["currentPlayer"] != -1 and $game["skipTime"] != 0 and $game["players"] >= $game["playersToStart"]) { // Makes sure that this game requires the skip turn part.
                    $time = time();
                    if (dbRequest2("SELECT timeLeft FROM golfGame WHERE ID='$id'", "timeLeft")[0] <= 0) { // Checks if the time is up for the player not being active. 
                        $orderID = $game["currentPlayer"];
                        $swap1 = rand(1, $game["cardNumber"]);
                        $swap2 = (rand(0, 1)) ? "discard" : 'deck';
                        $orderUser = dbRequest2("SELECT user FROM golfGamePlayers WHERE orderID=$orderID and gameID='$id'", "user")[0];
                        moveCard($orderUser, $id, $swap1, $swap2);
                        $game = dbRequest2("SELECT * FROM golfGame WHERE ID='$id'")[0];
                        // Adds one to the amount of turns skipped and checks if the limit has been passed.
                        $turnsSkipped = dbRequest2("SELECT turnsSkipped FROM golfGamePlayers WHERE orderID=$orderID and gameID='$id'", "turnsSkipped")[0] + 1;
                        dbCommand("UPDATE golfGamePlayers SET turnsSkipped=$turnsSkipped WHERE orderID=$orderID and gameID='$id'");
                    } elseif (dbRequest2("SELECT turnStartTime FROM golfGame WHERE ID='$id'")[0] != $time) { // Checks if a new second has passed
                        dbCommand("UPDATE golfGame SET turnStartTime=$time WHERE ID='$id'");
                        dbCommand("UPDATE golfGame SET timeLeft=timeLeft-1 WHERE ID='$id'");
                    }
                }
                if (dbRequest2("SELECT upToDate FROM golfGamePlayers WHERE gameID='$id' and user='$USERNAME' and upToDate")) {
                    echo "No change";
                    header('alt-svc: h3=":443"; ma=86400, h3-29=":443"; ma=86400, h3-28=":443"; ma=86400, h3-27=":443"; ma=86400', true);
                    http_response_code(304);
                } else if ($game["players"] >= $game["playersToStart"]) {
                    $skipUptoDate = false;
                    $players = dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' ORDER BY orderID ASC");
                    $selfPlayer =  dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and user='$USERNAME'")[0];
                    $selfPlayerID = $selfPlayer["orderID"];
                    // Checks if bots still have to be added
                    $bots = dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and bot=1");
                    $bots = count($bots);
                    $faker = Faker\Factory::create();
                    for ($i = $bots; $i < $game["bots"]; $i++) {
                        $name = $faker->name;
                        while (dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and user='$name'")) {
                            $name = $faker->name;
                        }
                        dbCommand("INSERT INTO golfGamePlayers VALUES ('$id', 1, '$name', 0, -1, 'waiting', 0, 0, 1)");
                    }
                    if ($selfPlayer["lastMode"] == "waiting") { // Makes sure the server knows that the player is now ready.
                        if (!dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and not lastMode='waiting'")) { // Starts the game if neccessary.
                            $game = readyGame($id);
                            $players = dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' ORDER BY orderID ASC");
                            $selfPlayer =  dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and user='$USERNAME'")[0];
                            $selfPlayerID = $selfPlayer["orderID"];
                        }
                        dbCommand("UPDATE golfGamePlayers SET lastMode='' WHERE gameID='$id' and user='$USERNAME'");
                    }
                    if (!dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and lastMode='waiting'")) { // Checks if all players are ready
                        $id = $game["ID"];
                        $length = count($players);
                        $roundOver = false;
                        for ($i = 0; $i < $length; $i++) { // Will addd some extra data to the game
                            $name = $players[$i]["user"];
                            $players[$i]["cards"] = dbRequest2("SELECT card, cardPlacement FROM golfGameCards WHERE gameID='$id' and user='$name' and faceUp");
                            $players[$i]["currentGamePoints"] = calculatePoints($name, $game["ID"]);
                            if ($players[$i]["lastMode"] != "eliminated") {
                                if (!dbRequest2("SELECT * FROM golfGameCards WHERE gameID=$id and user='$name' and not faceUp")) { // Will check if a player has flipped all their cards.
                                    $roundOver = $name;
                                }
                            }
                        }
                        if ($roundOver) { // Checks if the round is over
                            dbCommand("UPDATE golfGamePlayers SET upToDate=0 WHERE gameID='$id' AND bot=0");
                            dbCommand("UPDATE golfGamePlayers SET lastMode='roundOver' WHERE gameID='$id' AND bot=1");
                            // Gives the player who flips the last card the multiplierForFlip
                            if (!dbRequest2("SELECT * FROM golfGamePlayers WHERE lastMode='roundOver' and gameID='$id'")) { // Checks if this is the first player done
                                $newMultiplier = $selfPlayer["multiplier"] * $game["multiplierForFlip"];
                                dbCommand("UPDATE golfGamePlayers SET multiplier='$newMultiplier' WHERE gameID='$id' and user='$roundOver'");
                            }
                            $length = count($players);
                            $id = $game["ID"];
                            // Will uncover every card
                            dbCommand("UPDATE golfGameCards SET faceUp=1 WHERE gameID='$id'");
                            for ($i = 0; $i < $length; $i++) {
                                $name = $players[$i]["user"];
                                $players[$i]["cards"] = dbRequest2("SELECT card, cardPlacement FROM golfGameCards WHERE gameID='$id' and user='$name' and faceUp");
                                $players[$i]["currentGamePoints"] = calculatePoints($name, $game["ID"]);
                            }
                            $action = "roundOver";
                        } elseif ($selfPlayerID === $game["currentPlayer"]) { // Checks if it is the persons turn
                            $action = "switch";
                        } else {
                            $action = "";
                            // Checks if it is a bots turn
                            $currentPlayer = $game["currentPlayer"];
                            $playerData = dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and orderID='$currentPlayer' and bot=1");
                            if ($currentPlayer != -1 and count($playerData) > 0) {
                                $deckCard2 = json_decode($game["discard"]);
                                $deckCard = 3453455;
                                if (count($deckCard2) > 0) {
                                    $deckCard = $deckCard2[count($deckCard2) - 1] % 13;
                                }
                                $deckDuplicate = false; // Stores if the deck card would cancel out one of the bots cards
                                $user = $playerData[0]["user"];
                                $cards = dbRequest2("SELECT * FROM golfGameCards WHERE gameID='$id' and user='$user' AND faceUp=1");
                                $maxCard = -1;
                                $maxCardID = -1;
                                foreach ($cards as $card) {
                                    $cardNumber = $card["card"] % 13;
                                    $cardNumber2 = $cardNumber + 13;
                                    $cardNumber3 = $cardNumber + 26;
                                    $cardNumber4 = $cardNumber + 39;
                                    $cardPlacement = $card["cardPlacement"];
                                    if ($deckCard == $cardNumber) {
                                        $deckDuplicate = true;
                                    } elseif ( $cardNumber != 12 and $cardNumber > $maxCard and !dbRequest2("SELECT * FROM golfGameCards WHERE gameID='$id' and user='$user' AND faceUp=1 AND (card='$cardNumber' OR card='$cardNumber2' OR card='$cardNumber3' OR card='$cardNumber4') AND cardPlacement!='$cardPlacement'")) {
                                        $maxCard = $cardNumber;
                                        $maxCardID = $cardPlacement;
                                    }
                                }
                                $swap2 = "deck";
                                if ($deckDuplicate or ($deckCard < 3 ) or $deckCard == 12) {
                                    $swap2 = "discard";
                                }
                                if ($maxCardID == -1 or $maxCard < 4) {
                                    $maxCardID = dbRequest2("SELECT * FROM golfGameCards WHERE gameID='$id' and user='$user' AND faceUp=0")[0]["cardPlacement"];
                                }
                                moveCard($user, $id, $maxCardID, $swap2);
                                $skipUptoDate = true;
                            }
                        }
                        if ($selfPlayer["lastMode"] == "eliminated") { // Makes sure that eliminated player does not get uneliminated
                            $action = "eliminated";
                        }
                        dbCommand("UPDATE golfGamePlayers SET lastMode='$action' WHERE gameID='$id' and user='$USERNAME'");
                        $id = $game["ID"];
                        if (!dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and (lastMode='switch' or lastMode='')")) { // The code for when a new round is started
                            if (dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and not lastMode='eliminated'")) { // Makes sure that not all the players are eliminated
                                $length = count($players);
                                $resetPoints = $game["resetPoints"];
                                for ($i = 0; $i < $length; $i++) { // Will calculate points for every player and add them to the total
                                    $name = $players[$i]["user"];
                                    $newPoints = $players[$i]["points"] + calculatePoints($name, $game["ID"]);
                                    if ($resetPoints > 0) { // Makes sure that resetPoints is enabled
                                        if ($newPoints % $resetPoints == 0) { // Checks if the points are at a resetable amount
                                            $newPoints = 0;
                                        }
                                    }
                                    dbCommand("UPDATE golfGamePlayers SET points=$newPoints WHERE gameID=$id and user='$name'");
                                }
                                $game = readyGame($id);
                            }
                        }
                        $game = reshuffleDeck($game);
                        $deck = json_decode($game["deck"]);
                        $discard = json_decode($game["discard"]);
                        $gameData = array( // Creates an array full of information about the game
                            "rules" => array(
                                "flipNumber" => $game["flipNumber"],
                                "cardNumber" => $game["cardNumber"],
                                "multiplierForFlip" => $game["multiplierForFlip"],
                                "pointsToEnd" => $game["pointsToEnd"]
                            ),
                            "currentPlayer" => $game["currentPlayer"],
                            "turnStartTime" => $game["turnStartTime"],
                            "discard" => $discard,
                            "discardCard" => array_key_last($discard),
                            "deckSize" => sizeof($deck),
                            "players" => $players,
                            "action" => $action // Used to say the current action the player should do
                        );
                        echo json_encode($gameData);
                        if (!$skipUptoDate) {
                            dbCommand("UPDATE golfGamePlayers SET upToDate=1 WHERE gameID='$id' and user='$USERNAME'");
                        }
                    } else {
                        echo "[]";
                    }
                } else {
                    echo "[]";
                }
                dbCommand("UPDATE golfGame SET locked=0 WHERE ID='$id'");
            } else {
                http_response_code(404);
                echo "Game does not exist";
            }
        } else {
            http_response_code(401);
            echo "You are not in this game";
        }
    } elseif (array_key_exists("swap", $_POST) and array_key_exists("swap2", $_POST) and array_key_exists("game", $_POST)) { // Used to swap 2 cards in the game
        $response = moveCard($USERNAME, $_POST["game"], $_POST["swap"], $_POST["swap2"]);
        http_response_code($response["code"]);
        echo $response["text"];
    } elseif (array_key_exists("join", $_POST)) { // Used to join a game.
        $id = intval($_POST["join"]);
        $game = dbRequest2("SELECT * FROM golfGame WHERE ID='$id'");
        // goes through every error condition and responds with the correct response
        if ($game) { // Checks if game exists
            $game = $game[0];
            if (dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and user='$USERNAME'")) { // Checks if player has already joined the game
                http_response_code(409);
                echo "You already joined game";
            } elseif ($game["players"] >= $game["playersToStart"]) { // Checks if the game is already full
                http_response_code(410);
                echo "Game is full";
            } elseif ($game["password"]) { // Checks if game requires a password
                if (array_key_exists("password", $_POST)) { // Checks if password is given
                    if ($game["password"] === $_POST["password"]) {
                        dbCommand("INSERT INTO golfGamePlayers VALUES ('$id', 1, '$USERNAME', 0, -1, 'waiting', 0, 0, 0)");
                        $newPlayers = count(dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id'"));
                        dbCommand("UPDATE golfGame SET players=$newPlayers WHERE id=$id");
                        echo "Joined game";
                        writeLog(15, "$USERNAME joined game #$id as the $newPlayers player with ip of $address");
                    } else {
                        http_response_code(401);
                        echo "Wrong password";
                    }
                } else {
                    http_response_code(400);
                    echo "Password required";
                }
            } else {
                dbCommand("INSERT INTO golfGamePlayers VALUES ('$id', 1, '$USERNAME', 0, -1, 'waiting', 0, 0, 0)");
                $newPlayers = count(dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id'"));
                dbCommand("UPDATE golfGame SET players=$newPlayers WHERE id=$id");
                echo "Joined game";
                writeLog(15, "$USERNAME joined game #$id as the $newPlayers player with ip of $address");
            }
        } else {
            http_response_code(404);
            echo "Game not found";
        }
    } elseif (array_key_exists("create", $_POST) and array_key_exists("cardNumber", $_POST) and array_key_exists("flipNumber", $_POST) and array_key_exists("playersToStart", $_POST) and array_key_exists("multiplierForFlip", $_POST) and array_key_exists("decks", $_POST) and array_key_exists("skipTime", $_POST) and array_key_exists("skipTurns", $_POST)) { // Used to create a new room
        $password = "";
        $name = $_POST["create"];
        $cardNumber = intval($_POST["cardNumber"]);
        $flipNumber = intval($_POST["flipNumber"]);
        $multiplierForFlip = floatval($_POST["multiplierForFlip"]);
        $playersToStart = intval($_POST["playersToStart"]);
        $bots = intval($_POST["bots"]);
        $pointsToEnd = intval($_POST["pointsToEnd"]);
        $decks = intval($_POST["decks"]);
        $skipTime = intval($_POST["skipTime"]);
        $skipTurns = intval($_POST["skipTurns"]);
        $resetPoints = intval($_POST["resetPoints"]);
        $time = time();
        if (array_key_exists("password", $_POST)) {
            $password = $_POST["password"];
        }
        // Goes through every value and makes sure that they are all valid
        if (!$name) {
            http_response_code(400);
            echo "No game name given";
        } elseif ($cardNumber <= 0) {
            http_response_code(400);
            echo "You need more than 0 cards";
        } elseif ($flipNumber <= 0) {
            http_response_code(400);
            echo "You need to flip more than 0 cards";
        } elseif ($playersToStart <= 0) {
            http_response_code(400);
            echo "You need to have more than 0 players";
        } elseif ($bots < 0) {
            http_response_code(400);
            echo "You can't have less than 0 bots";
        } elseif ($pointsToEnd <= 0) {
            http_response_code(400);
            echo "The points to end need to be more than 0";
        } elseif (($playersToStart + $bots) * $cardNumber >= 52 * $decks) {
            http_response_code(400);
            echo "There are not enough cards in a deck for that amount of players and cards";
        } elseif ($decks > 50 or $decks < 1) {
            http_response_code(400);
            echo "You can only have 50 decks";
        } elseif ($skipTime < 0) {
            http_response_code(400);
            echo "Only positive times until skip are allowed";
        } elseif ($skipTurns < 0) {
            http_response_code(400);
            echo "Only positive amount of turns skiped are allowed";
        } elseif ($resetPoints < 0) {
            http_response_code(400);
            echo "Reset points must be a positive number or 0";
        } else {
            dbCommand("INSERT INTO golfGame (deck, discard, cardNumber, flipNumber, multiplierForFlip, pointsToEnd, name, password, players, playersToStart, currentPlayer, turnStartTime, decks, skipTime, timeLeft, skipTurns, resetPoints, bots) VALUES ('[]', '[]', $cardNumber, $flipNumber, $multiplierForFlip, $pointsToEnd, '$name', '$password', 0, $playersToStart, -1, $time, $decks, $skipTime, $skipTime, $skipTurns, $resetPoints, $bots)");
            echo "Created Game";
            writeLog(14, "$USERNAME created game for $playersToStart players, $bots bots, $cardNumber cards, $decks decks, and name $name with ip of $address");
        }
    } elseif (array_key_exists("forceUpdate", $_GET)) { // Used to force the update the next time an update is called
        $id = $_GET["forceUpdate"];
        dbCommand("UPDATE golfGamePlayers SET upToDate=0 WHERE gameID=$id and user='$USERNAME'");
        echo "Force update for game #$id";
    } else {
        http_response_code(400);
        echo "Invalid command";
    }
} else {
    http_response_code(401);
    echo "Not logged in";
}
