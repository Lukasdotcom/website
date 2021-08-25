<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family - Dice game
    </title>
    <?php
    $DESCRIPTION = "A simple dice game";
    require_once '../include/all.php';
    ?>
    <script type='text/javascript' src='/javascript/functions.js'></script>
    <script type='text/javascript' src='index.js'></script>
</head>

<body>
    <?php
    require_once '../include/menu.php';
    echo "<div class='main'>";
    ?>
    <h1>Dice Game</h1>
    <div id='winGame' class='popup'>
        <div class='popup-content'>
            <h1>Guess the roll to win!</h1>
            <p>You have <c id='winGameRollsLeft'></c> rolls left.</p>
            <p id='winGameText'></p>
            <input id='guess' type='number' min='1' max='20' value='10'></input>
            <button id='winGameRoll'>Roll 20 sided die</button>
        </div>
    </div>
    <div class='column2'>
        <p>You have rolled the dice <c id="diceRolls">0</c> times.</p>
        <p>You have <c id='points'>0</c> point(s).</p>
        <button id='roll'>Roll dice</button>
        <h2 id='multiplier'></h2>
        <div id='rollResult'></div>
        <h3>Other Purchases</h3>
        <div id='otherShop'></div>
    </div>
    <div class='column2'>
        <h3>Permanent Upgrades</h3>
        <div id='reset'></div>
        <h3>Dice Upgrades</h3>
        <div id='diceShop'></div>
    </div>
    <button class='red' onclick="completeReset()">Reset Game Completely</button>
    </div>
</body>

</html>