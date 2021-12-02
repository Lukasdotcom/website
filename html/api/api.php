<?php
// Used for the setup for all apis to get their basic info
header('Content-Type: application/json');
if(array_key_exists("key", $_GET)) { // Will check if a get key is given and then will use the key as the cookie variable
    $_COOKIE["user"] = $_GET["key"];
} elseif (array_key_exists("key", $_POST)) {
    $_COOKIE["user"] = $_POST["key"];
} else {
    $_COOKIE["user"] = "";
}
require_once "../include/functions.php";
function missingPrivilege($USERNAME) { // Used to see if the user is not logged in or if the user does not have the privilege to do that action
    if ($USERNAME) {
        http_response_code(403);
        echo "Forbidden";
    } else {
        http_response_code(401);
        echo "Not logged in";
    }
}
function loggedIn($USERNAME) {
    if (! $USERNAME) {
        http_response_code(401);
        echo "Not logged in";
        exit();
    }
}