<?php
if($_GET["key"]) { // Will check if a key is given and then will use the key as the cookie variable
    $_COOKIE["user"] = $_GET["key"];
}
include 'include/functions.php';
if($_GET["internet"] == "data") {
    $data = dbRequest2("SELECT * FROM internet ORDER BY id Asc");
    echo json_encode($data);
} elseif ($PRIVILEGE["internet"] and $_GET["internet"] === "button"){
    echo "Unsupported";
} elseif ($PRIVILEGE["internet"] and $_GET["internet"] === "edit") {
    $id = intval($_GET["id"]);
    if ($_GET["idNegative"] === "True") {
        $id = $id * -1;
    }
    $startHour = intval($_GET["startHour"]);
    $startMinute = intval($_GET["startMinute"]);
    $endHour = intval($_GET["endHour"]);
    $endMinute = intval($_GET["endMinute"]);
    $expire = intval($_GET["expire"]);
    if (dbRequest2("SELECT * FROM internet WHERE id='$id'")) {
        dbEdit("internet", [["hour", $startHour], ["minute", $startMinute], ["hour2", $endHour], ["minute2", $endMinute], ["expire", $expire]], ["id", $id], 0);
    } else {
        dbAdd([$startHour, $startMinute, $endHour, $endMinute, $expire, $id], "internet");
    }
} elseif ($PRIVILEGE["internet"] and $_GET["internet"] === "delete") {
    $id = intval($_GET["id"]);
    if ($_GET["idNegative"] === "True") {
        $id = $id * -1;
    }
    dbCommand("DELETE FROM internet WHERE id='$id'");
}