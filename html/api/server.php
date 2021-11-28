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
        echo shell_exec("vcgencmd measure_temp");
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}