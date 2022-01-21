<?php
require_once "api.php";
loggedIn($USERNAME);
if (array_key_exists("get", $_GET)) {
    echo json_encode(dbRequest2("SELECT * FROM cookies WHERE username='$USERNAME'"));
} else if (array_key_exists("delete", $_POST)) {
    $key = $_POST["delete"];
    dbCommand("DELETE FROM cookies WHERE username='$USERNAME' and cookie='$key'");
    echo "Deleted session";
    writeLog(21, "User $USERNAME revoked session with ip $address");
} else if (array_key_exists("create", $_POST)) {
    $expire = intval($_POST["create"]);
    $cookie = $USERNAME;
    $cookie .= rand();
    $cookie = sanitize(substr(sha1($cookie), 5));
    dbCommand("INSERT INTO cookies VALUES ('$cookie', '$USERNAME', $expire, '$address')");
    echo "Created new session";
    writeLog(22, "User $USERNAME created session with ip $address");
} else {
    http_response_code(400);
    echo "Invalid command";
}