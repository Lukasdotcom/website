<?php
include "api.php";
/**
 * @param string the user you want to search for if this is empty the logged in user is used
 * @return string of the user or will quit if the user does not exist
 */
function checkUser($user) {
    global $USERNAME;
    if ($user) {
        if (!dbRequest2("SELECT * FROM users WHERE username='$user'")) {
            echo "User $user does not exist";
            http_response_code(405);
            exit;
        } else {
            return $user;
        }
    } else {
        return $USERNAME;
    }
}
if ($_GET["type"] === "view") { // Will return all privileges the user has in a list
    if ($PRIVILEGE["editUser"] or $USERNAME === $_GET["user"] and $USERNAME) {
        $user = checkUser($_GET["user"]);
        // Will request all privileges
        $request = dbRequest2("SELECT privilege FROM privileges WHERE username='$user'", $result="privilege");
        // Will make sure that it returns valid json
        if ($request) {
            echo json_encode($request);
        } else {
            echo "[]";
        }
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_POST["type"] === "edit") { // Make sure to add logging
    if ($PRIVILEGE["editUser"] or $USERNAME === $_GET["user"] and $USERNAME) {
        $user = checkUser($_POST["user"]);
        dbCommand("DELETE FROM privileges WHERE username='$user'");
        foreach($PRIVILEGE as $PRIV => $bool) {
            if ($bool){
                if ($_POST[$PRIV] and $_POST[$PRIV] !== "false" and $_POST[$PRIV] !== "False") {
                    dbCommand("INSERT INTO privileges VALUES ('$user', '$PRIV')");
                }
            }
        echo "saved";
        }
    } else {
        missingPrivilege($USERNAME);
    }
}else {
    http_response_code(400);
    echo "Invalid command";
}
