<!DOCTYPE html>

<html dir="ltr" lang="en">

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
                        echo "https" . '://' . $_SERVER["HTTP_HOST"]; ?>/cookieClicker/index.js";
        script.id = "hostname";
        document.head.appendChild(script);})();</div>
    <p>After that you just have to pick a room, you will see everyone's number of cookies in the same room, if you want to use a public room the closest to that is just not entering anything for the room</p>
    <p>If you would like to use this as a user script use the red box below</p>
    <div style="border-width:3px;border-style:solid;padding:2px;border-color:red;">
        // ==UserScript==<br>
        // @name Cookie Clicker<br>
        // @namespace <?php
                        // Will echo out the hostname
                        echo "https" . '://' . $_SERVER["HTTP_HOST"]; ?>/cookieClicker<br>
        // @version 2024-08-09<br>
        // @description Multiplayer Cookie Clicker<br>
        // @author lukasdotcom<br>
        // @match https://orteil.dashnet.org/cookieclicker/<br>
        // @grant none<br>
        // ==/UserScript==<br>
        var script = document.createElement('script');<br>
        script.src = "<?php
                        // Will echo out the hostname
                        echo "https" . '://' . $_SERVER["HTTP_HOST"]; ?>/cookieClicker/index.js";<br>
        script.id = "hostname";<br>
        document.head.appendChild(script);</div>
    <p>If you would like to use this with the chrome or firefox extension Cookie Clicker Mod Manager use the link in the blue box below</p>
    <div style="border-width:3px;border-style:solid;padding:2px;border-color:blue;">
        <?php
        // Will echo out the hostname
        echo "https" . '://' . $_SERVER["HTTP_HOST"]; ?>/cookieClicker/modManager.js</div>
    <?php
    createImage("cookieClicker", "Picture of the addon.");
    ?>
    <h3>Terms of Use or license</h3>
    <p>
        <?php
        if (file_exists("../../LICENSE")) {
            echo file_get_contents("../../LICENSE");
        } else {
        ?>
            Could not find license!
        <?php
        }
        ?>
    </p>
    </div>
</body>

</html>