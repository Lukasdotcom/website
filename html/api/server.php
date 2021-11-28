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
} else {
    http_response_code(400);
    echo "Invalid command";
}