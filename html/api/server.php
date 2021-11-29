<?php
require_once "api.php";
if ($_GET["uptime"]) { # Will return some status info for a server.
    if ($PRIVILEGE["serverStatus"]) {
        echo shell_exec("uptime");
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_GET["temp"]) { # Will return temprature.
    if ($PRIVILEGE["serverStatus"]) {
        echo file_get_contents("/sys/class/thermal/thermal_zone0/temp") / 1000;
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_GET["log"]) { # Allows for the requesting of specific logs can be used like the old api still just don't give time data and reverse the order.
    if ($PRIVILEGE["viewLog"]) {
        if ($_GET["startTime"]) {
            $startTime = intval($_GET["startTime"]);
            $startTime = "time>$startTime";
        } else {
            $startTime = "time>0";
        }
        if ($_GET["endTime"]) {
            $endTime = intval($_GET["endTime"]);
            $endTime = "and time<$endTime";
        }
        echo json_encode(dbRequest2("SELECT * FROM log WHERE $startTime $endTime"));
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}