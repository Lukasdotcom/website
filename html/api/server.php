<?php
require_once "api.php";
if (array_key_exists("uptime", $_GET)) { # Will return some status info for a server.
    if ($PRIVILEGE["serverStatus"]) {
        echo shell_exec("uptime");
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_exists("temp", $_GET)) { # Will return temprature.
    if ($PRIVILEGE["serverStatus"]) {
        if (file_exists("/sys/class/thermal/thermal_zone0/temp")) {
            echo file_get_contents("/sys/class/thermal/thermal_zone0/temp") / 1000;
        } else {
            echo "Unknown";
        }
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_exists("log", $_GET) and array_key_exists("startTime", $_GET) and array_key_exists("endTime", $_GET)) { # Allows for the requesting of specific logs can be used like the old api still just don't give time data and reverse the order.
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
} elseif (array_key_exists("restart", $_POST)) { // To delete an entry in log
    if ($PRIVILEGE["restartServer"]) {
        echo "Restarting";
        $restart = fopen("restart.json", "w");
        fclose($restart);
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_exists("update", $_POST)) { // To delete an entry in log
    if ($PRIVILEGE["updateServer"]) {
        echo "Updating.";
        $update = fopen("update.json", "w");
        fclose($update);
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}