<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
    <title>
        Golf
    </title>
    <?php
    $DESCRIPTION = "A way to play the card game golf online with your friends.";
    require_once '../include/all.php';
    ?>
    <script type='text/javascript' src='/javascript/functions.js'></script>
</head>

<body>
    <?php
    require_once '../include/menu.php';
    echo "<div class='main'>";
    ?>
    <h1>Golf</h1>
    <p>Golf is a very simple card game, for the specific rules go to <a href="https://bicyclecards.com/how-to-play/six-card-golf/" target="_blank" rel="noopener noreferrer">bicyclecards</a>. This version does have some small changes that are that you get out once you hit a certain amount of points, lastly the person who flips the last card gets double the points(you can change this multiplier) that they would normally get.</p>
    <?php
    if (! $USERNAME) {
        echo "<h2>You are not logged in. Please log in to Play.</h2>";
    } else {
        ?>
        <script type='text/javascript' src='index.js?v=1.0.0'></script>
        <p>Join Game below here:</p>
        <table id='games'>
        </table>
        <p>Create Game below here:</p>
        <div>
            <label for="name">Name: </label><input type='text' value='' id="name"><br>
            <label for="cardNumber">Cards: </label><input type='number' value='6' id="cardNumber"><br>
            <label for="flipNumber">Flipped Cards at Start: </label><input type='number' value='2' id="flipNumber"><br>
            <label for="playersToStart">Human Players: </label><input type='number' value='4' id="playersToStart"><br>
            <label for="bots">Bots: </label><input type='number' value='0' id="bots"><br>
            <label for="multiplierForFlip">Multiplier for flipping last card: </label><input type='number' value='2' id="multiplierForFlip"><br>
            <label for="pointsToEnd">Points to get out: </label><input type='number' value='100' id="pointsToEnd"><br>
            <label for="decks">Number of decks to use: </label><input type='number' value='1' id="decks"><br>
            <label for="skipTime">Amount of time to wait before player is skipped(0 means infinite): </label><input type='number' value='0' id="skipTime"><br>
            <label for="skipTurns">Amount of turns the player can skip(0 means infinite): </label><input type='number' value='0' id="skipTurns"><br>
            <label for="resetPoints">The number of points that when a multiple of this number is reached your points will reset(0 disables this): </label><input type='number' value='0' id="resetPoints"><br>
            <label for="password">Password(leave blank for none): </label><input type='password' value='' id="password"><br>
            <button id='create'>Create</button>
        </div>
    <?php
    }
    ?>
    </div>
</body>

</html>
