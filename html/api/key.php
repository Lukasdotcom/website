<?php
require_once "api.php";
loggedIn($USERNAME);
if (array_key_exists("get", $_GET)) {
    echo json_encode(dbRequest2("SELECT * FROM cookies WHERE username='$USERNAME'"));
} else if (array_key_exists("delete", $_POST)) {
    $key = $_POST["delete"];
    dbCommand("DELETE FROM cookies WHERE username='$USERNAME' and cookie='$key'");
    echo "Deleted session";
} else {
    http_response_code(400);
    echo "Invalid command";
}