<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family - Electricity Log
    </title>
    <?php
    $DESCRIPTION = "Log that contains when electricity is on and off.";
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
    $jsonData = array_reverse($jsonData);
    $length = sizeof($jsonData) - 1;
    $electricity = True;
    for ($i = $length; $i >= 0; $i -= 2) {
        if ($electricity == True) {
            echo "<a style='color: green'>Electricity on from ";
            $electricity = False;
        } else {
            echo "<a style='color: red'>Electricity off from ";
            $electricity = True;
        }
        $date = $jsonData[$i];
        $date2 = $jsonData[$i - 1];
        echo "$date[0] $date[1], $date[2] at $date[3]:$date[4]";
        echo " to ";
        echo "$date2[0] $date2[1], $date2[2] at $date2[3]:$date2[4]</a><br>";
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