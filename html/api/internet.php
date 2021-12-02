<?php
require_once "api.php";
if (array_key_exists("data", $_GET)) { // Will give internet data
    $data = dbRequest2("SELECT * FROM internet ORDER BY id Asc");
    echo json_encode($data);
} elseif (array_key_exists("edit", $_POST) and array_key_exists("startHour", $_POST) and array_key_exists("startMinute", $_POST) and array_key_exists("endHour", $_POST) and array_key_exists("endMinute", $_POST) and array_key_exists("expire", $_POST)) { // Will edit the internet data in the database
    if ($PRIVILEGE["internet"]) {
        $id = intval($_POST["edit"]);
        $startHour = intval($_POST["startHour"]);
        $startMinute = intval($_POST["startMinute"]);
        $endHour = intval($_POST["endHour"]);
        $endMinute = intval($_POST["endMinute"]);
        $expire = intval($_POST["expire"]);
        if (dbRequest2("SELECT * FROM internet WHERE id='$id'")) {
            dbEdit("internet", [["hour", $startHour], ["minute", $startMinute], ["hour2", $endHour], ["minute2", $endMinute], ["expire", $expire]], ["id", $id], 0);
            $startTime = $startHour . ":" . $startMinute;
            $endTime = $endHour . ":" . $endMinute;
            writeLog(11, "User $USERNAME changed internet schedule entry number $id to contents, from $startTime to $endTime with expiration of $expire.");
        } else {
            dbAdd([$startHour, $startMinute, $endHour, $endMinute, $expire, $id], "internet");
            $startTime = $startHour . ":" . $startMinute;
            $endTime = $endHour . ":" . $endMinute;
            writeLog(11, "User $USERNAME added internet schedule entry number $id with contents from $startTime to $endTime with expiration of $expire.");
        }
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_exists("delete", $_POST)) { // Will delete data from internet schedule
    if ($PRIVILEGE["internet"]) {
        $id = intval($_POST["delete"]);
        $input = dbRequest2("SELECT * FROM internet WHERE id='$id'");
        dbCommand("DELETE FROM internet WHERE id='$id'");
        $startTime = $input[0]["hour"] . ":" . $input[0]["minute"];
        $endTime = $input[0]["hour2"] . ":" . $input[0]["minute2"];
        $expire = $input[0]["expire"];
        writeLog(11, "User $USERNAME deleted internet schedule entry number $id with contents from $startTime to $endTime with expiration of $expire.");
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}