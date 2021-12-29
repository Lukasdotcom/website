<?php
require_once "api.php";
if (array_key_exists("save", $OGPOST)) {
    loggedIn($USERNAME);
    dbCommand("DELETE FROM localStorage WHERE username='$USERNAME'");
    dbCommand("INSERT INTO localStorage VALUES ('$USERNAME', ?)", [$OGPOST["save"]]);
    echo "Saved Preferences";
} elseif (array_key_exists("load", $_GET)) {
    loggedIn($USERNAME);
    $response = dbRequest2("SELECT * FROM localStorage WHERE username='$USERNAME'", "data");
    if ($response) {
        echo $response[0];
    } else {
        http_response_code(404);
        echo "No Save Found";
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}