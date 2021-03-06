<?php
require_once "api.php";
header("Access-Control-Allow-Origin: *"); // Used to allow Space 3's api to be used on any domain
if (array_key_exists("search", $OGGET)) { // Used for searching the database
    $searchTerm = $OGGET["search"];
    $searchTerm = "%$searchTerm%";
    $defaultLength = 100; # The default length that is returned
    $response = dbRequest2("SELECT id, owner, title, description, likes, downloads FROM space3 WHERE description LIKE ? or title LIKE ? or owner LIKE ? ORDER BY likes DESC, downloads DESC", $result="*", $prepare=[$searchTerm, $searchTerm, $searchTerm]);
    if (!array_key_exists("length", $_GET)) {
        $_GET["length"] = $defaultLength;
    }
    $response = array_slice($response, 0, $_GET["length"]);
    // Used to check if the user requesting this liked each result
    $length = count($response);
    $favorites = [];
    for($i=0;$i<$length;++$i) {
        $liked = false;
        if ($USERNAME) {
            $id = $response[$i]["id"];
            $liked = boolval(dbRequest2("SELECT * FROM space3likes WHERE id=$id and account='$USERNAME'"));
        }
        $response[$i]["liked"] = $liked;
        if ($liked or $response[$i]["owner"] == $USERNAME) {
            array_push($favorites, $response[$i]);
            unset($response[$i]);
        }
    }
    $response = array_merge($favorites, $response);
    echo json_encode($response);
} elseif (array_key_exists("update", $_POST) and array_key_exists("title", $_POST) and array_key_exists("description", $_POST)) { // Used to update or add to the space 3 addons
    loggedIn($USERNAME);
    if (array_key_exists("id", $_POST)) {
        $id = $_POST["id"];
        $info = dbRequest2("SELECT likes, downloads FROM space3 WHERE id='$id' and owner='$USERNAME'");
        $downloads = $info[0]["downloads"];
        $likes = $info[0]["likes"];
        if (array_key_exists("preferences", $OGPOST)) { // Will check if the preferences need to be updated
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
        dbCommand("INSERT INTO space3 (`id`, `owner`, `title`, `description`, `preferences`, `likes`, `downloads`) VALUES ('$id', '$USERNAME', ?, ?, ?, $likes, $downloads)", $prepare=[$OGPOST["title"], $OGPOST["description"], $newPreference]);
    } else {
        if (array_key_exists("preference", $OGPOST)) {
            dbCommand("INSERT INTO space3 (`owner`, `title`, `description`, `preferences`, `likes`, `downloads`) VALUES ('$USERNAME', ?, ?, ?, 0, 0)", $prepare=[$OGPOST["title"], $OGPOST["description"], $OGPOST["preferences"]]);
            echo "Added new preference";
        } else {
            http_response_code(400);
            echo "Invalid command";
        }
    }
} elseif (array_key_exists("delete", $_POST) and $USERNAME) { // Used to delete a preference
    $id = $_POST["delete"];
    dbCommand("DELETE FROM space3 WHERE id='$id' and owner='$USERNAME'");
    dbCommand("DELETE FROM space3likes WHERE id='$id'");
    echo "Preference number $id deleted";
} elseif (array_key_exists("like", $_POST) and $USERNAME) { // Used to like/unlike a preference
    $id = $_POST["like"];
    $info = dbRequest2("SELECT * FROM space3 WHERE id=$id");
    if ($info) {
        if ($info[0]["owner"] == $USERNAME) { // Makes sure that the owner is not liking their won preference.
            http_response_code(401);
            echo 'You can not like your own preference';
        } else {
            // Checks if this is a like or an unlike.
            $likes = dbRequest2("SELECT * FROM space3likes WHERE id=$id");
            if ($likes) {
                $count = count($likes);
            } else {
                $count = 0;
            }
            if (dbRequest2("SELECT * FROM space3likes WHERE id=$id and account='$USERNAME'")) {
                $count = $count - 1;
                dbCommand("DELETE FROM space3likes WHERE id=$id and account='$USERNAME'");
                echo "Unliked preference with id $id";
            } else {
                $count = $count + 1;
                dbCommand("INSERT INTO space3likes VALUES ($id, '$USERNAME')");
                echo "Liked preference with id $id";
            }
            dbCommand("UPDATE space3 SET likes='$count' WHERE id=$id");
        }
    } else {
        http_response_code(404);
        echo "Invalid preference";
    }
} elseif (array_key_exists("download", $_GET)) { // Used to download a preference
    $id = $_GET["download"];
    $response = dbRequest2("SELECT preferences, downloads FROM space3 WHERE id=$id");
    if ($response[0]) {
        $downloads = $response[0]["downloads"] + 1;
        dbCommand("UPDATE space3 SET downloads='$downloads' WHERE id=$id");
        echo $response[0]["preferences"];
    } else {
        http_response_code(400);
        echo "Did not find preference.";
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}