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
        echo "<label for='searchText'>Search:</label>
                <input id='searchText' placeholder='Search'></input>
                <button type='button' onClick='search(document.getElementById(`searchText`).value)'>Search</button><br>";
        echo '<form method="post" action="/log.php">
            <input type="submit" value="reload"><br>';
        $typeList = dbRequest("*", "logType", "", "", 2);
        $jsonTypeList = json_encode($typeList);
        foreach ($typeList as $logType) {
            $type = $logType["name"];
            $color = $logType["color"];
            echo "<div style='color: $color'><input id='$type' type='checkbox' name='$type' checked='yes'>$type</div>";
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
            $color = $type["color"];
            $category = $type["name"];
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
    }
    ?>
    </form>
        </div>
</body>

</html>