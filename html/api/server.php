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
} elseif (array_key_exists("restart", $_POST)) { // To delete an entry in log
    if ($PRIVILEGE["restartServer"]) {
        echo "Restarting";
        $restart = fopen("../restart.json", "w");
        fclose($restart);
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_exists("update", $_POST)) { // To delete an entry in log
    if ($PRIVILEGE["updateServer"]) {
        unlink("../updateInfo.log");
        echo "Updating.";
        $update = fopen("../update.json", "w");
        fclose($update);
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_exists("update", $_GET)) { // Gives the update text info for the previous update(the git response)
    if ($PRIVILEGE["updateServer"]) {
        if (file_exists("../updateInfo.log")) {
            echo file_get_contents("../updateInfo.log");
        } else {
            echo "No recent update";
            http_response_code(404);
        }
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}