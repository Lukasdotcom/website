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
    <div class='column2'>
        <p>You have rolled the dice <c id="diceRolls">0</c> times.</p>
        <p>You have <c id='points'>0</c> point(s).</p>
        <button id='roll'>Roll dice</button>
        <h2 id='multiplier'></h2>
        <div id='rollResult'></div>
    </div>
    <div class='column2'>
        <h3>Permanent Upgrades</h3>
        <div id='reset'></div>
        <h3 id='dice'>Dice Upgrades</h3>
        <div id='diceShop'></div>
    </div>
    <button class='red' onclick="completeReset()">Reset Game Completely</button>
    </div>
</body>

</html>