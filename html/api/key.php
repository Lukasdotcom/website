<?php
require_once "api.php";
loggedIn($USERNAME);
if (array_key_exists("get", $_GET)) {
    echo json_encode(dbRequest2("SELECT * FROM cookies WHERE username='$USERNAME'"));
} else if (array_key_exists("delete", $_POST)) {
    $key = $_POST["delete"];
    dbCommand("DELETE FROM cookies WHERE username='$USERNAME' and cookie='$key'");
    echo "Deleted session";
} else if (array_key_exists("create", $_POST)) {
    $expire = intval($_POST["create"]);
    $cookie = $USERNAME;
    $cookie .= rand();
    $cookie = sanitize(substr(sha1($cookie), 5));
    dbCommand("INSERT INTO cookies VALUES ('$cookie', '$USERNAME', $expire, '$address')");
    echo "Created new session";
} else {
    http_response_code(400);
    echo "Invalid command";
}