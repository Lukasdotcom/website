<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family - Internet
    </title>
    <?php
    $DESCRIPTION = "The internet schedule for the Schaefer family";
    include 'include/all.php';
    ?>
</head>

<body>
    <?php
    include 'include/menu.php';
    echo "<div class='main'>
        <h1>Internet Schedule</h1>
        <script type='text/javascript' src='javascript/internet.js'></script>  
        <table><tr><th>Priority</th><th>Start Time</th><th>End Time</th><th>Expiration Time</th><th></th></tr>";
    $jsonInfo = file_get_contents("http://127.0.0.1/api.php?internet=data");
    $schedule = json_decode($jsonInfo, true);
    foreach ($schedule as $one) {
        $priority = $one['id'];
        $startHour = $one['hour'];
        $startMinute = $one['minute'];
        $endHour = $one['hour2'];
        $endMinute = $one['minute2'];
        $expire = $one["expire"];
        if($PRIVILEGE["internet"]) {
            $startTimeText = "<input type='number' id='$priority.startHour' value='$startHour'>:<input type='number' id='$priority.startMinute' value='$startMinute'>";
        } else {
            $startTimeText = "$startHour:$startMinute";
        }
        if($PRIVILEGE["internet"]) {
            $endTimeText = "<input type='number' id='$priority.endHour' value='$endHour'>:<input type='number' id='$priority.endMinute' value='$endMinute'>";
        } else {
            $endTimeText = "$endHour:$endMinute";
        }
        if($PRIVILEGE["internet"]) {
            $expireText = "<input style='width: 120px' type='number' id='$priority.expire' value='$expire'>";
        } else {
            $expireText = "$expire";
        }
        echo "<tr id='$priority.row'>
                <form>
                    <td>$priority</td>
                    <td>
                        $startTimeText
                    </td><td>
                        $endTimeText
                    </td><td>
                        $expireText
                    </td><td>";
                if($PRIVILEGE["internet"]) {
                    echo "<button type='button' onClick='save(`$priority`)'>✓</button>
                        <div class='red'>
                            <button type='button' onClick='remove(`$priority`)'>✗</button>";
                }  
                    echo "</div>
                    </td>
                </form>
            </tr>";
    }
    echo "</table>";
    ?>
    <button onClick='button()'>Change Internet Status for Next Hour</button>
    <p id='saveStatus' style='color: green'> </p>
    </div>
</body>

</html>