<?php
require_once "api.php";
/**
 * Calculates the points for a certain player
 * @param string $user The username of the player.
 * @param string $game The game of the player.
 * @return int The amount of points the player has currently.
 */
function calculatePoints($user, $game) {
    $cardValues = [1, -2, 3, 4, 5, 6, 7, 8, 9, 10, 10, 10, 0];
    $cardAmount = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ,0];
    $points = 0;
    $cards = dbRequest2("SELECT card FROM golfGameCards WHERE gameID='$game' and user='$user' and faceUp");
    if ($cards) {
        foreach ($cards as $card) {
            $cardAmount[$card["card"]%13] ++;
        }
        foreach ($cardAmount as $card=>$amount) {
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
function reshuffleDeck($game) { 
    $deck = json_decode($game["deck"]);
    if (!$deck) {
        $ID = $game["ID"];
        $discard = json_decode($game["discard"]);
        $deck = array_pop($discard);
        shuffle($discard);
        $discard = json_encode($discard);
        $deck = json_encode($deck);
        dbCommand("UPDATE golfGame SET deck='$deck', discard='$discard' WHERE ID='$ID'");
        $game = dbRequest2("SELECT * FROM golfGame WHERE ID='$ID'")[0];
    }
    return $game;
}
/**
 * Gets a round ready to start.
 * @param string $game The game of the player.
 */
function readyGame($game) {
    $deck = array(); # Used to get the deck ready
    for ($i=0;$i<52;$i++) {
        array_push($deck, $i);
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
        for ($i=0;$i<$playerCount;$i++) { # Goes through every player and gives them their cards and puts them in the correct order.
            $name = $players[$i]["user"];
            if ($players[$i]["points"] < $gameData["pointsToEnd"]) { // Checks if the player is still in the game.
                $cardsToFlip = $flippedCards;
                for ($j=1;$j<=$cards;$j++) {
                    $card = array_pop($deck);
                    if ($cardsToFlip > 0) {
                        dbCommand("INSERT INTO golfGameCards VALUES ('$game', '$name', '$card', '$j', '1')");
                        $cardsToFlip --;
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
    if (array_key_exists("game", $_GET)){ // Gets the log
        $data = dbRequest2("SELECT name, password, players, playersToStart, cardNumber, flipNumber, multiplierForFlip, pointsToEnd, ID FROM golfGame WHERE players != playersToStart");
        foreach ($data as $id => $entry) { // Makes sure to not leak the password
            if ($entry["password"]) {
                $data[$id]["password"] = true;
            }
        }
        echo json_encode($data);
    } elseif (array_key_exists("update", $_GET)) {
        $id = $_GET["update"];
        if (dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and user='$USERNAME'")) { // WIll check if the player is playing the game
            $game = dbRequest2("SELECT * FROM golfGame WHERE ID='$id'");
            if ($game) { // Will check if the game exists
                $game = $game[0];
                if (dbRequest2("SELECT upToDate FROM golfGamePlayers WHERE gameID='$id' and user='$USERNAME' and upToDate") and ! array_key_exists("forceNew", $_GET)) {
                    echo "No change";
                    http_response_code(304);
                } else if ($game["players"] >= $game["playersToStart"]) {
                    $players = dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' ORDER BY orderID ASC");
                    $selfPlayer =  dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and user='$USERNAME'")[0];
                    $selfPlayerID = $selfPlayer["orderID"];
                    if ($selfPlayer["lastMode"] == "waiting") { // Makes sure the server knows that the player is now ready.
                        if (! dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and not lastMode='waiting'")) { // Starts the game if neccessary.
                            $game = readyGame($id);
                        }
                        dbCommand("UPDATE golfGamePlayers SET lastMode='' WHERE gameID='$id' and user='$USERNAME'");
                    }
                    if (! dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and lastMode='waiting'")) { // Checks if all players are ready
                        $id = $game["ID"];
                        $length = count($players);
                        $roundOver = false;
                        for ($i=0;$i<$length; $i++) { // Will addd some extra data to the game
                            $name = $players[$i]["user"];
                            $players[$i]["cards"] = dbRequest2("SELECT card, cardPlacement FROM golfGameCards WHERE gameID='$id' and user='$name' and faceUp");
                            $players[$i]["currentGamePoints"] = calculatePoints($name, $game["ID"]);
                        }
                        if ($selfPlayer["lastMode"] != "eliminated") {
                            if (! dbRequest2("SELECT * FROM golfGameCards WHERE gameID=$id and user='$USERNAME' and not faceUp")) { // Will check if the player has flipped all their cards.
                                $roundOver = true;
                            }
                        }
                        if ($roundOver) { // Checks if the round is over
                            dbCommand("UPDATE golfGamePlayers SET upToDate=0 WHERE gameID='$id'");
                            // Gives the player who flips the last card the multiplierForFlip
                            if (! dbRequest2("SELECT * FROM golfGamePlayers WHERE lastMode='roundOver' and gameID='$id'")) { // Checks if this is the first player done
                                $newMultiplier = $selfPlayer["multiplier"] * $game["multiplierForFlip"];
                                dbCommand("UPDATE golfGamePlayers SET multiplier='$newMultiplier' WHERE gameID='$id' and user='$USERNAME'");
                            }
                            $length = count($players);
                            $id = $game["ID"];
                            // Will uncover every card
                            dbCommand("UPDATE golfGameCards SET faceUp=1 WHERE gameID='$id'");
                            for ($i=0;$i<$length; $i++) {
                                $name = $players[$i]["user"];
                                $players[$i]["cards"] = dbRequest2("SELECT card, cardPlacement FROM golfGameCards WHERE gameID='$id' and user='$name' and faceUp");
                                $players[$i]["currentGamePoints"] = calculatePoints($name, $game["ID"]);
                            }
                            $action = "roundOver";
                        } elseif ($selfPlayerID === $game["currentPlayer"]) { // Checks if it is the persons turn
                            $action = "switch";
                        } else {
                            $action = "";
                        }
                        if ($selfPlayer["lastMode"] == "eliminated") { // Makes sure that eliminated player does not get uneliminated
                            $action = "eliminated";
                        }
                        dbCommand("UPDATE golfGamePlayers SET lastMode='$action' WHERE gameID='$id' and user='$USERNAME'");
                        $id = $game["ID"];
                        if (! dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and (lastMode='switch' or lastMode='')")) { // The code for when a new round is started
                            if (dbRequest2("SELECT * FROM golfGamePlayers WHERe gameID='$id' and not lastMode='eliminated'")) {
                                $length = count($players);
                                for ($i=0;$i<$length; $i++) { // Will calculate points for every player and add them to the total
                                    $name = $players[$i]["user"];
                                    $newPoints = $players[$i]["points"] + calculatePoints($name, $game["ID"]);
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
                        if (! array_key_exists("forceNew", $_GET)) { // Will make sure the browser is not confused
                            dbCommand("UPDATE golfGamePlayers SET upToDate=1 WHERE gameID='$id' and user='$USERNAME'");
                        }
                    } else {
                        echo "[]"; 
                    }
                } else {
                    echo "[]";
                }
            } else {
                http_response_code(404);
                echo "Game does not exist";
            }
        } else {
            http_response_code(401);
            echo "You are not in this game";
        }
    } elseif (array_key_exists("swap", $_POST) and array_key_exists("swap2", $_POST) and array_key_exists("game", $_POST)) { // Used to swap 2 cards in the game
        $id = $_POST["game"];
        $self = dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id' and user='$USERNAME'");
        $game = dbRequest2("SELECT * FROM golfGame WHERE ID='$id'");
        if ($self and $game) {
            $game = $game[0];
            $self = $self[0];
            $selfID = $self["orderID"];
            $gameCurrentPlayer = $game["currentPlayer"];
            if ($gameCurrentPlayer != $selfID) { # Makes sure that it is the players turn
                http_response_code(403);
                echo "It is not your turn";
                exit();
            }
            $cardPlacement = $_POST["swap"];
            $cards = dbRequest2("SELECT * FROM golfGameCards WHERE user='$USERNAME' and gameID='$id'");
            $cardSwap = dbRequest2("SELECT * FROM golfGameCards WHERE user='$USERNAME' and gameID='$id' and cardPlacement='$cardPlacement'");
            if (!(($_POST["swap2"] == "discard" or $_POST["swap2"] == "deck") and $cardSwap)) { # Makes sure that player gave a valid request
                http_response_code(400);
                echo "Invalid request";
                exit();
            }
            $game = reshuffleDeck($game);
            $deck = json_decode($game["deck"]);
            $discard = json_decode($game["discard"]);
            if ($_POST["swap2"] == "discard" and $discard) { // Checks if the player wants to switch the discard pile or deck.
                $newCard = array_pop($discard);
            } else {    
                $newCard = array_pop($deck);
            }
            array_push($discard, $cardSwap[0]["card"]);
            dbCommand("UPDATE golfGameCards SET card=$newCard, faceUp=1 WHERE user='$USERNAME' and gameID='$id' and cardPlacement='$cardPlacement'");
            $deck = json_encode($deck);
            $discard = json_encode($discard);
            do { // Will make sure the next picked player is a valid player that exists and is not eliminated.
                $gameCurrentPlayer ++;
                if ($game["playersToStart"] <= $gameCurrentPlayer) {
                    $gameCurrentPlayer = 0;
                }
            } while (dbRequest2("SELECT * FROM golfGamePlayers WHERE lastMode='eliminated' and orderID='$gameCurrentPlayer' and gameID='$id'"));
            $type = $_POST["swap2"];
            $card = $_POST["swap"];
            echo "Switched card #$card with $type"; // Responds with what action was just done.
            $time = time();
            dbCommand("UPDATE golfGame SET deck='$deck', discard='$discard', currentPlayer='$gameCurrentPlayer', turnStartTime='$time' WHERE id=$id");
            dbCommand("UPDATE golfGamePlayers SET upToDate=0 WHERE gameID='$id'");
        } else {
            http_response_code(404);
            echo "Game does not exist";
        }
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
                        dbCommand("INSERT INTO golfGamePlayers VALUES ('$id', 1, '$USERNAME', 0, -1, 'waiting', 0)");
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
                dbCommand("INSERT INTO golfGamePlayers VALUES ('$id', 1, '$USERNAME', 0, -1, 'waiting', 0)");
                $newPlayers = count(dbRequest2("SELECT * FROM golfGamePlayers WHERE gameID='$id'"));
                dbCommand("UPDATE golfGame SET players=$newPlayers WHERE id=$id");
                echo "Joined game";
                writeLog(15, "$USERNAME joined game #$id as the $newPlayers player with ip of $address");
            }
        } else {
            http_response_code(404);
            echo "Game not found";
        }
    } elseif (array_key_exists("create", $_POST) and array_key_exists("cardNumber", $_POST) and array_key_exists("flipNumber", $_POST) and array_key_exists("playersToStart", $_POST) and array_key_exists("multiplierForFlip", $_POST)) { # Used to create a new room
        $password = "";
        $name = $_POST["create"];
        $cardNumber = intval($_POST["cardNumber"]);
        $flipNumber = intval($_POST["flipNumber"]);
        $multiplierForFlip = floatval($_POST["multiplierForFlip"]);
        $playersToStart = $_POST["playersToStart"];
        $pointsToEnd = intval($_POST["pointsToEnd"]);
        $time = time();
        if (array_key_exists("password", $_POST)) {
            $password = $_POST["password"];
        }
        $cardLimit = 100;
        $playerLimit = 100;
        if ($name and $cardNumber>0 and $cardNumber<$cardLimit and $flipNumber>0 and $flipNumber<$cardNumber and $playersToStart>0 and $playersToStart<$playerLimit and $pointsToEnd>0) { // Makes sure that the game has valid inputs.
            dbCommand("INSERT INTO golfGame (deck, discard, cardNumber, flipNumber, multiplierForFlip, pointsToEnd, name, password, players, playersToStart, currentPlayer, turnStartTime) VALUES ('[]', '[]', $cardNumber, $flipNumber, $multiplierForFlip, $pointsToEnd, '$name', '$password', 0, $playersToStart, -1, $time)");
            echo "Created Game";
            writeLog(14, "$USERNAME created game for $playersToStart players and $cardNumber cards with ip of $address");
        } else {
            http_response_code(400);
            echo "ERROR invalid settings";
        }
    } else {
        http_response_code(400);
        echo "Invalid command";
    }
} else {
    http_response_code(401);
    echo "Not logged in";
}