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
    <p>You have rolled the dice <c id="diceRolls">0</c> times.</p>
    <h3>Permanent Upgrades</h3>
    <div id='reset'></div>
    <h3 id='dice'>Dice Shop</h3>
    <div id='diceShop'></div>
    <button id='roll'>Roll dice</button>
    <div id='rollResult'></div>
    <br>
    <button onclick="completeReset()">Reset Game Completely</button>
    </div>
</body>

</html>