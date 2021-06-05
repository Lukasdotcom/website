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
    } elseif (array_search("viewLog", $PRIVILEGE) === false) {
        header("Refresh:3; url=/index.php");
        echo "<h2>Forbidden redirecting...</h2>";
    } else {
        echo "<h1>Server Log</h1>";
        if ($PRIVILEGE["deleteLog"]) {
            if ($_POST["clear"]) {
                $possibleDelete = dbRequest("message", "log", "time", $_POST["time"], 0);
                if (array_search($OGPOST["message"], $possibleDelete) !== NULL and array_search($OGPOST["message"], $possibleDelete) !== false) {
                    dbRemove("log", ["message", "time"], [$OGPOST["message"], $_POST["time"]], 0);
                }
            } elseif ($_POST["reset"]) {
                dbRemove("log", 1, 1, 0);
                echo "<p>Log has been cleared<p>";
            } elseif ($_POST["fixLog"]) {
                dbRemove("logType", "type", 2000, 1);
                $jsonInfo = file_get_contents("logTypes.json");
                $jsonData = json_decode($jsonInfo, true);
                foreach ($jsonData as $name) {
                    dbAdd([$name["type"], $name["name"], $name["color"]], "logType");
                }
            }
        }
        // Will echo the server log if logged in
        $logData = array_reverse(dbRequest("*", "log", "", "", 2));
        echo "<table>";
        echo "<tr><th>Category</th><th>Message</th><th>Time Stamp</th><th>Time</th></tr>";
        foreach ($logData as $log) {
            $time = $log["time"];
            $date = date("m-d-Y", $time);
            $clockTime = date("H:i:s", $time);
            $message = $log["message"];
            $type = dbRequest("*", "logType", "type", $log["type"], 0)[0];
            $color = $type["color"];
            $category = $type["name"];
            echo "<tr style='color: $color'><td>$category</td><td>$message </td><td>$time</td><td>$clockTime at $date</td>";
            if ($PRIVILEGE["deleteLog"]) {
                echo "<td style='color: white'><form action='/log.php' method='post'> <input type='hidden' name='message' value='$message'> <input type='hidden' name='time' value='$time'> <button type='submit' name='clear' value='true'>Clear</button></form></td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo '<form method="post" action="/log.php">
                    <input type="submit" value="reload"><br>';
        if (array_search("deleteLog", $PRIVILEGE)) {
            echo '<button name="reset" value="True" type="submit">reset log</button>';
        }
    }
    ?>
    <br>
    <button name="fixLog" value="True" type="submit">Fix log types<br></button>
    <--Press this if you have missing values in the first column of the table </form>
        </div>
</body>

</html>