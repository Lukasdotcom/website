<?php
require_once "api.php";
if (array_key_exists("get", $_GET)) {
    loggedIn($USERNAME);
    echo json_encode(dbRequest2("SELECT * FROM cookies WHERE username='$USERNAME'"));
} else {
    http_response_code(400);
    echo "Invalid command";
}