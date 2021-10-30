<?php
require_once "api.php";
if ($_POST["save"] and $USERNAME) {
    dbCommand("DELETE FROM localStorage WHERE username='$USERNAME'");
    dbCommand("INSERT INTO localStorage VALUES ('$USERNAME', ?)", [$OGPOST["save"]]);
    echo "Saved Preferences";
} elseif ($_POST["load"]) {
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