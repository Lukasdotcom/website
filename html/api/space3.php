<?php
require_once "api.php";
if (gettype($OGGET["search"]) == "string") {
    $searchTerm = $OGGET["search"];
    $searchTerm = "%$searchTerm%";
    echo json_encode(dbRequest2("SELECT * FROM space3 WHERE description LIKE ?", $result="*", $prepare=[$searchTerm]));
} else {
    http_response_code(400);
    echo "Invalid command";
}