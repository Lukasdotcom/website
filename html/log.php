<!DOCTYPE html>
<html>

<head>
    <title>
        Schaefer Family - Log Page
    </title>
    <?php
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
    } elseif (array_search("viewLog", $PRIVILEGE) === false) {
        header("Refresh:3; url=/index.php");
        echo "<h2>Forbidden redirecting...</h2>";
    } else {
        echo "<h1>Server Log</h1>";
        // Will echo the server log if logged in
        $logData = array_reverse(dbRequest("*", "log", "", "", 2));
        echo "<table>";
        echo "<tr><th>Category</th><th>Message</th><th>Time Stamp</th><th>Time</th></tr>";
        foreach ($logData as $log) {
            $time = $log["time"];
            $date = date("m-d-Y", $time);
            $clockTime = date("H:i", $time);
            $message = $log["message"];
            $type = dbRequest("*", "logType", "type", $log["type"], 0)[0];
            $color = $type["color"];
            $category = $type["name"];
            echo "<tr style='color: $color'><td>$category</td><td>$message at </td><td>$time</td><td>$clockTime at $date</td></tr>";
        }
        echo "</table>";
        echo '<form method="get" action="/log.php">
                    <input type="submit" value="reload"><br>';
    }

    ?>
    </div>
</body>

</html>