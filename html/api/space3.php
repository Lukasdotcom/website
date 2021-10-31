<?php
require_once "api.php";
if (gettype($OGGET["search"]) == "string") { // Used for searching the database
    $searchTerm = $OGGET["search"];
    $searchTerm = "%$searchTerm%";
    $defaultLength = 20;
    $response = dbRequest2("SELECT * FROM space3 WHERE description LIKE ?", $result="*", $prepare=[$searchTerm]);
    if (intval($_GET["length"])) {
        $response = array_slice($response, 0, $_GET["length"]);
    } else {
        $response = array_slice($response, 0, $defaultLength);
    }
    echo json_encode($response);
} else {
    http_response_code(400);
    echo "Invalid command";
}