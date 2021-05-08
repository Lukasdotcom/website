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
        // Will clear the log.json file except for the first entry. This entry contains important info
        echo "<h1>Server Log</h1>";
        if ($_GET["reset"] == True and array_search("deleteLog", $PRIVILEGE)) {
            header("Refresh:2; url=log.php");
            echo "<h3>Resetting Log</h3>";
            $jsonInfo = file_get_contents("log.json");
            $jsonFile = fopen("log.json", "w");
            $jsonData = json_decode($jsonInfo, true);
            $important = json_encode([$jsonData[0]]);
            fwrite($jsonFile, $important);
            fclose($jsonFile);
        } else {
            // Will echo the server log if logged in and create some buttons that link to other portions or do actions
            $jsonInfo = file_get_contents("log.json");
            $jsonData = json_decode($jsonInfo, true);
            $first = True;
            foreach ($jsonData as $data) {
                if ($first) {
                    $first = False;
                    continue;
                }
                $date = $data[2];
                echo "$data[1] at $date[3]:$date[4]:$date[5] on $date[0] $date[1], $date[2]<br>";
            }
            echo '<form method="get" action="/log.php">
                    <input type="submit" value="reload"><br>';

            if (array_search("deleteLog", $PRIVILEGE)) {
                echo '<button name="reset" value="True" type="submit<br>">reset log</button>';
            }
        }
    }

    ?>
    </div>
</body>

</html>