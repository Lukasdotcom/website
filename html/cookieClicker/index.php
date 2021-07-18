<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family
    </title>
    <?php
    $DESCRIPTION = "Cookie clicker addon located here";
    require_once '../include/all.php';
    ?>
</head>

<body>
    <?php
    require_once '../include/menu.php';
    echo "<div class='main'>";
    ?>
    <h1>Welcome to the main page for the cookie clicker addon</h1>
    <p>This add on allows you to have a multiplayer cookie clicker expirience. To use it you just have to create a bookmark that has the text in the box below. Then go to the cookie clicker website and click on it.</p>
    <div style="border-width:3px;border-style:solid;padding:2px;border-color:green;">
    javascript: (() => {
    var script = document.createElement('script');
    script.src = "<?php
    // Will echo out the hostname
    echo $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["HTTP_HOST"];?>/cookieClicker/index.js";
    script.id = "hostname";
    document.head.appendChild(script);})();</div>
    <p>After that you just have to pick a room if you want to use a public room the closest to that is just not entering anything for the room</p>
    <img src='/img/cookieClicker.png' alt='Picture of the addon' width="100%">
</div>
</body>

</html>