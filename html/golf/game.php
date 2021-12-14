<!DOCTYPE html>

<html>

<head>
    <title>
        Golf
    </title>
    <?php
    $DESCRIPTION = "A way to play the card game golf online with your friends.";
    require_once '../include/all.php';
    ?>
    <link rel="stylesheet" href="/css/golf.css">
    <script type='text/javascript' src='/javascript/functions.js'></script>
    <script type='text/javascript' src='game.js'></script>
</head>

<body>
    <?php
    require_once '../include/menu.php';
    echo "<div class='main'>";
    echo "<script>var joined=false</script>";
    if (! array_key_exists("game", $_GET)) {
        header("Refresh:1; url=/golf/index.php", true);
        echo "<h2>No game id is given</h2>";
        exit();
    } else {
        $id = $_GET["game"];
        $gameInfo = dbRequest2("SELECT * FROM golfGame WHERE ID='$id'");
    }
    if (! $gameInfo) {
        header("Refresh:1; url=/golf/index.php", true);
        echo "<h2>Game does not exist</h2>";
    } elseif (! $USERNAME) {
        echo "<h2>You are not logged in redirecting...</h2>";
        header("Refresh:3; url=/login.php?redirect=golf_game.php?game=", true);
        http_response_code(401);
    } else {
        $gameInfo = $gameInfo[0];
        $name = $gameInfo["name"];
        echo "<h1> Your in room $name</h1>";
        $id = $_GET["game"];
        if (! dbRequest2("SELECT * FROM golfGamePlayers WHERE user='$USERNAME' and gameID='$id'")) { # Checks if the game has already been joined
            if ($gameInfo["password"]) {
                echo "Password: <input type='password' id='password'><div width='10em'></div>";
            }
            echo "<button onClick='join()'>join</button>";
        } else {
            echo "<script>var player='$USERNAME'</script>";
            ?>
            <script>var joined=true</script>
            <h3 id='wait'>Waiting...</h3>
            <div id='game' style="display: none;">
                <div id='decks' class='card-container'>
                    <input type="image" id='discard' src='/img/deck/.jpg'>
                    <input type="image" id='deck' src='/img/deck/back.jpg'>
                </div>
                <div id='left-arrow' style="margin-left:10px"></div>
                <div id='right-arrow' class='right' style="margin-right:10px"></div>
                <h3 class="center"><c id='name'></c>'s Points: <c id='points'></c></h3>
                <h3 class="center">Points this turn: +<c id='newPoints'></c></h3>
                <div id='cards' class='card-container'>
                </div>
                <div class="center">
                    <button id='submitMove' style="display: none; font-size: 40px;" onclick="submitMove()">Submit Move</button>
                    <button id='continue' style="display: none; font-size: 40px;">Continue</button>
                </div>
            </div>
            <?php
        }
    }
    ?>
    </div>
</body>

</html>