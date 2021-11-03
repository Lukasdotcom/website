<?php
require_once "api.php";
header("Access-Control-Allow-Origin: *"); // Used to allow Space 3's api to be used on any domain
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
} elseif ($_POST["update"] and $USERNAME) { // Used to update or add to the space 3 addons
    if ($_POST["id"]) {
        $id = $_POST["id"];
        if ($OGPOST["preferences"]) { // Will check if the preferences need to be updated
            $newPreference = $OGPOST["preferences"];
            echo "Overwrote preference with id $id.";
        } else {
            $newPreference = dbRequest2("SELECT preferences FROM space3 WHERE id='$id' and owner='$USERNAME'", $result="preferences");
            if ($newPreference) {
                $newPreference = $newPreference[0];
            } else {
                http_response_code(401);
                echo "You do not own this preference.";
                exit();
            }
            echo "Updated description/title for preference with id $id.";
        }
        dbCommand("DELETE FROM space3 WHERE id='$id' and owner='$USERNAME'");
        dbCommand("INSERT INTO space3 (`id`, `owner`, `title`, `description`, `preferences`) VALUES ('$id', '$USERNAME', ?, ?, ?)", $prepare=[$OGPOST["title"], $OGPOST["description"], $newPreference]);
    } else {
        dbCommand("INSERT INTO space3 (`owner`, `title`, `description`, `preferences`) VALUES ('$USERNAME', ?, ?, ?)", $prepare=[$OGPOST["title"], $OGPOST["description"], $OGPOST["preferences"]]);
        echo "Added new preference with id $id";
    }
} elseif ($_POST["delete"] and $USERNAME) { // Used to delete a preference
    dbCommand("DELETE FROM space3 WHERE id='$id' and owner='$USERNAME'");
}else {
    http_response_code(400);
    echo "Invalid command";
}