<!DOCTYPE html>
<html>

<head>
    <title>
        Schaefer Family - Log Page
    </title>
    <?php
    $DESCRIPTION = "Server log.";
    require_once '../include/all.php';
    ?>
</head>

<body>
    <?php
    require_once '../include/menu.php';
    echo "<div class='main'>";
    if ($USERNAME == NULL) {
        echo "<h2>You are not logged in redirecting...</h2>";
        header("Refresh:3; url=/login.php");
        http_response_code(401);
    } elseif (!$PRIVILEGE["viewLog"]) {
        http_response_code(403);
        header("Refresh:3; url=/index.php");
        echo "<h2>Forbidden redirecting...</h2>";
    } else {
        echo "<script type='text/javascript' src='index.js'></script>
            <script type='text/javascript' src='/javascript/functions.js'></script>";
        echo "<h1>Server Log</h1>";
        echo "<label for='searchText'>Search:</label>
                <input id='searchText' placeholder='Search' oninput='search(document.getElementById(`searchText`).value)'></input><br>";
        echo '<form method="post" action="/log/">
            <input type="submit" value="reload"></form>';
        $typeList = dbRequest("*", "logType", "", "", 2);
        $jsonTypeList = json_encode($typeList);
        $id = 0;
        $typeLength = sizeof($typeList);
        echo "<script>var typeLength = $typeLength</script>";
        echo "<script>var types = JSON.parse('$jsonTypeList'); var typeLength = types.length;</script>";
        echo "<button id='collapseCategories' type='button' onClick='collapseCategories()'>Uncollapse Categories</button><br>";
        // Creates all categories to search for
        foreach ($typeList as $logType) {
            $type = $logType["name"];
            // Decodes the prefereneces
            $color = $logType["color"];
            $checked = "checked";
            echo "<div style='color: $color;' id='$type.text'><input type='checkbox' id='$type' name='$type' $checked>$type; Color: <input type='color' value='$color' id='$type.color'><button type='button' onClick='resetColor(document.getElementById(`searchText`).value, `$type`, `$id`)'>Reset Color</button></div>";
            $id++;
        }
        // Will echo the server log if logged in
        $logData = array_reverse(dbRequest("*", "log", "", "", 2));
        if ($PRIVILEGE["restartServer"]) {
            echo "<button onClick='restart()' >Restart Server</button>";
        }
        if ($PRIVILEGE["updateServer"]) {
            echo "<button onClick='update()' >Update Server</button>";
        }
        if ($PRIVILEGE["serverStatus"]) {
            echo "<h2 class='offline' style='display: none;'>You are offline</h2>";
            echo "<h3>Server Status</h3>";
            echo "<p>Time: <c id='uptime'></c></p>";
            echo "<p> Temp=<c id='temp'></c>˚C</p>";
            echo "<script>updateStats();setInterval(updateStats, 1000);</script>";
        }
        echo "<table id='log'>";
        echo "<tr id='tableHeader'><th>Category</th><th>Message</th><th>Time Stamp</th><th>Time</th></tr>";
        echo "</table>";
    }
    ?>
    </div>
</body>

</html>