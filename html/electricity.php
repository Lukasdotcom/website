<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family
    </title>
    <?php
    include 'include/all.php';
    ?>
</head>

<body>
    <?php
    include 'include/menu.php';
    echo "<div class='main'>";
    ?>
    <h1>Electricity Log</h1>
    <?php
    // Gets the data.json file and goes through every line to output the data.
    $jsonInfo = file_get_contents("data.json");
    $jsonData = json_decode($jsonInfo, true);
    $internet = True;
    $electricity = True;
    $jsonData = array_reverse($jsonData);
    foreach ($jsonData as $date) {
        if ($internet == True) {
            $internet = False;
            if ($electricity == True) {
                echo "<a style='color: green'>Electricity on from ";
                $electricity = False;
            } else {
                echo "<a style='color: red'>Electricity off from ";
                $electricity = True;
            }
            echo "$date[0] $date[1], $date[2] at $date[3]:$date[4]";
            echo " to ";
        } else {
            $internet = True;
            echo "$date[0] $date[1], $date[2] at $date[3]:$date[4]</a><br>";
        }
    }
    // Resets the outage reporter when requested by deleting all of data.json and using the last entry and putting that in twice into the json
    if ($PRIVILEGE["deleteElectricity"]) {
        if ($_POST["reset"] == True) {
            writeLog(4, "Electricity log cleared by $USERNAME or $address");
            echo "Resetting Outage Reporter";
            $jsonInfo = file_get_contents("data.json");
            $jsonFile = fopen("data.json", "w");
            $jsonData = json_decode($jsonInfo, true);
            $important = $jsonData[count($jsonData) - 1];
            $important = json_encode([$important, $important]);
            fwrite($jsonFile, $important);
            fclose($jsonFile);
            header("Refresh:1; url=/electricity.php");
        } else {
            echo '<form method="post" action="/electricity.php">
                    <button name="reset" value="True" type="submit<br>">reset outage reporter</button>
                </form>';
        }
    }
    ?>
    </div>
</body>

</html>
