<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
    <title>
        Schaefer Family - Internet
    </title>
    <?php
    $DESCRIPTION = "The internet schedule for the Schaefer family";
    include '../include/all.php';
    ?>
</head>

<body>
    <?php
    include '../include/menu.php';
    $jsonInfo = file_get_contents("http://127.0.0.1/api/internet.php?data=data");
    $schedule = json_decode($jsonInfo, true);
    $topPriority = end($schedule)["id"];
    if (!$topPriority) {
        $topPriority = 1;
    }
    echo "<div class='main'>
        <h1>Internet Schedule</h1>
        <script type='text/javascript' src='index.js'></script>
        <script type='text/javascript' src='/javascript/functions.js'></script>
        <script> var topPriority = $topPriority; </script>
        <table id='internetTable'><tr><th>Priority</th><th>Start Time</th><th>End Time</th><th>Expiration Time</th><th></th></tr>";
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
            $expireText = "<input step='1' type='datetime-local' id='$priority.expire'><script>date = new Date($expire*1000); document.getElementById('$priority.expire').value = date.toISOString().split('.')[0];</script>";
        } else {
            $expireText = "$expire";
        }
        echo "<tr id='$priority.row'>
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
            </tr>";
    }
    echo "</table>";
    if($PRIVILEGE["internet"]) {
        echo "<button onClick='addRow()'>Add another row to internet schedule</button>";
    }
    ?>
    <p id='saveStatus' style='color: green'> </p>
    </div>
</body>

</html>