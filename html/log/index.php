<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <title>
        Log Page
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
    if (!$USERNAME) {
        echo "<h2>You are not logged in redirecting...</h2>";
        header("Refresh:3; url=/login.php", true);
        http_response_code(401);
    } elseif (!$PRIVILEGE["viewLog"]) {
        http_response_code(403);
        header("Refresh:3; url=/index.php", true);
        echo "<h2>Forbidden redirecting...</h2>";
    } else {
        echo "<script type='text/javascript' src='index.js?v=1.0.0'></script>
            <script type='text/javascript' src='/javascript/functions.js'></script>";
        echo "<h1>Server Log</h1>";
        echo "<label for='searchText'>Search:</label>
                <input id='searchText' placeholder='Search' oninput='search(document.getElementById(`searchText`).value)'></input><br>";
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
            echo "<div style='color: $color;' id='$type.text'>
                    <input type='checkbox' onClick='search($(`#searchText`).val())' id='$type' name='$type' $checked>
                    $type; Color: <input type='color' onClick='search($(`#searchText`).val())' value='$color' id='$type.color'>
                    <button type='button' onClick='resetColor(document.getElementById(`searchText`).value, `$type`, `$id`)'>Reset Color</button>
                </div>";
            $id++;
        }
        if ($PRIVILEGE["restartServer"]) {
            echo "<button onClick='restart()' >Restart Server</button>";
        }
        if ($PRIVILEGE["updateServer"]) {
            echo "<button onClick='update()' >Update Server</button>";
            echo "<p id='updateText'>No recent update</p>";
        }
        if ($PRIVILEGE["deleteLog"]) {
            echo "<script>var deleteLog = true</script>";
        } else {
            echo "<script>var deleteLog = false</script>";
        }
        if ($PRIVILEGE["serverStatus"]) { // Used to detect if the server has recently had an error and displays it to the user
            echo "<h2 class='offline' style='display: none;'>You are offline</h2>";
            echo "<h3>Server Status</h3>";
            echo "<p>Time: <c id='uptime'></c></p>";
            echo "<p> Temp=<c id='temp'></c>˚C</p>";
            echo "<script>var serverStatus = true;</script>";
            if (file_exists("../error.log") and $PRIVILEGE["deleteError"]) {
                if ($_POST["delete"] == "error") {
                    unlink("../error.log");
                    writeLog(4, "Error log deleted by $USERNAME or $address");
                } else {
                    echo "<h2>Previous Server Error</h2>";
                    echo "<p>";
                    echo htmlspecialchars(file_get_contents("../error.log"));
                    echo "</p>";
                    echo '<form method="post" action="/log/">
                        <button name="delete" value="error" type="submit">Delete Error Log.</button>
                        </form><br>';
                }
            }
        }
    ?>
        <table id='log'>
            <tr id='tableHeader'>
                <th>Category</th>
                <th>Message</th>
                <th>Time Stamp</th>
                <th>Time</th>
            </tr>
        </table>
        <div id="load">
            <button id='loadMore'>Load More</button>
            <label for="days">Days to load more: </label><input type='number' id="days" value="7">
            <br>
            <button id='loadAll'>Load All</button>
        </div>
    <?php
    }
    ?>
    </div>
</body>

</html>