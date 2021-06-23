<?php
if($_GET["key"]) { // Will check if a get key is given and then will use the key as the cookie variable
    $_COOKIE["user"] = $_GET["key"];
} else {
    $_COOKIE["user"] = $_POST["key"];
}
include 'include/functions.php';
function missingPrivilege($USERNAME) {
    if ($USERNAME) {
        http_response_code(403);
        echo "Forbidden";
    } else {
        http_response_code(401);
        echo "Not logged in";
    }
}
if($_GET["internet"] == "data") {
    $data = dbRequest2("SELECT * FROM internet ORDER BY id Asc");
    echo json_encode($data);
} elseif ($_GET["internet"] === "button"){
    echo "Unsupported. This message will eventually be removed";
    http_response_code(410);
} elseif ($_POST["internet"] === "edit") {
    if ($PRIVILEGE["internet"]) {
        $id = intval($_POST["id"]);
        $startHour = intval($_POST["startHour"]);
        $startMinute = intval($_POST["startMinute"]);
        $endHour = intval($_POST["endHour"]);
        $endMinute = intval($_POST["endMinute"]);
        $expire = intval($_POST["expire"]);
        if (dbRequest2("SELECT * FROM internet WHERE id='$id'")) {
            dbEdit("internet", [["hour", $startHour], ["minute", $startMinute], ["hour2", $endHour], ["minute2", $endMinute], ["expire", $expire]], ["id", $id], 0);
            writeLog(11, "User $USERNAME changed internet schedule entry number $id");
        } else {
            dbAdd([$startHour, $startMinute, $endHour, $endMinute, $expire, $id], "internet");
            writeLog(11, "User $USERNAME added internet schedule entry number $id");
        }
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_POST["internet"] === "delete") {
    if ($PRIVILEGE["internet"]) {
        $id = intval($_POST["id"]);
        dbCommand("DELETE FROM internet WHERE id='$id'");
        writeLog(11, "User $USERNAME deleted internet schedule entry number $id");
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}