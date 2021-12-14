<?php
require_once "api.php";
if (array_key_exists("data", $_GET)){ // Gets the log
    if ($PRIVILEGE["viewLog"]) {
        echo json_encode(dbRequest("*", "log", "", "", 2));
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_exists("log", $_GET)) { # Allows for the requesting of specific logs can be used like the old api still just don't give time data and reverse the order.
    if ($PRIVILEGE["viewLog"]) {
        if (array_key_exists("startTime", $_GET)) {
            $startTime = intval($_GET["startTime"]);
            $startTime = "time>$startTime";
        } else {
            $startTime = "time>0";
        }
        if (array_key_exists("endTime", $_GET)) {
            $endTime = intval($_GET["endTime"]);
            $endTime = "and time<$endTime";
        } else {
            $endTime = "";
        }
        echo json_encode(dbRequest2("SELECT * FROM log WHERE $startTime $endTime"));
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_exists("remove", $_POST) and array_key_exists("time", $_POST) and array_key_exists("message", $_POST)) { // To delete an entry in log
    if ($PRIVILEGE["deleteLog"]) {
        $possibleDelete = dbRequest("message", "log", "time", $_POST["time"], 0);
        if (array_search($OGPOST["message"], $possibleDelete) !== NULL and array_search($OGPOST["message"], $possibleDelete) !== false) {
            dbRemove("log", ["message", "time"], [$OGPOST["message"], $_POST["time"]], 0);
        } else {
            http_response_code(404);
            echo "Log entry does not exist";
        }
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}