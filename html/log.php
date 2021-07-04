<!DOCTYPE html>
<html>

<head>
    <title>
        Schaefer Family - Log Page
    </title>
    <?php
    $DESCRIPTION = "Server log.";
    include 'include/all.php';
    ?>
</head>

<body>
    <?php
    include 'include/menu.php';
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
        echo "<h1>Server Log</h1>";
        // Includes the search
        $cookieSearch = $OGCOOKIE["logSearch"];
        echo "<label for='searchText'>Search:</label>
                <input id='searchText' value='$cookieSearch' placeholder='Search'></input>
                <button type='button' onClick='search(document.getElementById(`searchText`).value)'>Search</button><br>";
        echo '<form method="post" action="/log.php">
            <input type="submit" value="reload"></form>';
        $typeList = dbRequest("*", "logType", "", "", 2);
        // Decodes the cookie that stores preferences
        $typeCookie = json_decode($OGCOOKIE["log"]);
        $jsonTypeList = json_encode($typeList);
        $id = 0;
        $typeLength = sizeof($typeList);
        echo "<script>var typeLength = $typeLength</script>";
        echo "<script>var types = JSON.parse('$jsonTypeList'); var typeLength = types.length;</script>";
        echo "<button id='collapseCategories' type='button' onClick='collapseCategories()'>Collapse Categories</button><br>";
        // Creates all categories to search for
        foreach ($typeList as $logType) {
            $type = $logType["name"];
            // Decodes the prefereneces
            if ($typeCookie[$id][1]) {
                $color = sanitize($typeCookie[$id][1]);
            } else {
                $color = $logType["color"];
            }
            if ($typeCookie[$id][0] === false) {
                $checked = "";
            } else {
                $checked = "checked";
            }
            echo "<div style='color: $color' id='$type.text'><input type='checkbox' id='$type' name='$type' $checked>$type; Color: <input type='color' value='$color' id='$type.color'><button type='button' onClick='resetColor(document.getElementById(`searchText`).value, `$type`, `$id`)'>Reset Color</button></div>";
            $id++;
        }
        echo "<input id='deleted' type='hidden' name='$type'>";
        // Will echo the server log if logged in
        $logData = array_reverse(dbRequest("*", "log", "", "", 2));
        echo "<script type='text/javascript' src='javascript/log.js'></script>
            <script type='text/javascript' src='javascript/functions.js'></script>";
        echo "<table id='log'>";
        echo "<tr><th>Category</th><th>Message</th><th>Time Stamp</th><th>Time</th></tr>";
        $id = 0;
        foreach ($logData as $log) {
            $time = $log["time"];
            $date = date("m-d-Y", $time);
            $clockTime = date("H:i:s", $time);
            $message = $log["message"];
            $type = dbRequest("*", "logType", "type", $log["type"], 0)[0];
            $category = $type["name"];
            if ($typeCookie[$log["type"]][1]) {
                $color = sanitize($typeCookie[$log["type"]][1]);
            } else {
                $color = $type["color"];
            }
            echo "<tr id='$id' style='color: $color'><td id='$id.category'>$category</td><td id='$id.message' >$message </td><td id='$id.time' >$time</td><td id='$id.clockTime' >$clockTime at $date</td>";
            if ($PRIVILEGE["deleteLog"]) {
                echo "<td id='$id.button' style='color: white'><button type='button' onClick='remove(`$message`, `$time`, `$id`)'>Delete</button><br></td>";
            }
            echo "</tr>";
            $id += 1;
        }
        $id -= 1;
        echo "<script>var logLength = $id;</script>";
        echo "</table>";
        if ($cookieSearch) {
            echo "<script>search($cookieSearch)</script>";
        }
        if ($_COOKIE["collapseCategories"]) {
            echo "<script>collapseCategories()</script>";
        }
    }
    ?>
    </div>
</body>

</html>