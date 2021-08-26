<!DOCTYPE html>

<html>

<head>
    <title>
        Cookie Clicker Addon
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
    <p>If you would like to use this as a user script use the red box below</p>
    <div style="border-width:3px;border-style:solid;padding:2px;border-color:red;">
    // ==UserScript==<br>
    // @name Cookie Clicker Online<br>
    // @include /https?://orteil.dashnet.org/cookieclicker/<br>
    // ==/UserScript==<br>
    var script = document.createElement('script');<br>
    script.src = "<?php
    // Will echo out the hostname
    echo $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["HTTP_HOST"];?>/cookieClicker/index.js";<br>
    script.id = "hostname";<br>
    document.head.appendChild(script);</div>
    <?php
    createImage("cookieClicker", "Picture of the addon.");
    ?>
    <h3>Terms of Use or license</h3>
    <p>
    <?php 
    echo file_get_contents("../../LICENSE");
    ?>
    </p>
</div>
</body>

</html>