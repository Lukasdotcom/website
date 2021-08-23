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
    <h3 id='dice'>Dice Shop</h3>
    <div id='diceShop'></div>
    <button id='roll'>Roll dice</button>
    <div id='rollResult'></div>
    </div>
</body>

</html>