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
} elseif ($_POST["type"] === "edit") { // Used to edit the users privileges
    if ($PRIVILEGE["editUser"] or $USERNAME === $_GET["user"] and $USERNAME) {
        $user = checkUser($_POST["user"]);
        // Gets the previous privileges
        $oldPriv = dbRequest2("SELECT privilege FROM privileges WHERE username='$user'", "privilege");
        // Goes through every privilege the user has and sees what the user wants on the new user they are editing
        foreach($PRIVILEGE as $PRIV => $bool) {
            if ($bool){
                if ($_POST[$PRIV] and $_POST[$PRIV] !== "false" and $_POST[$PRIV] !== "False") {
                    if (array_search($PRIV, $oldPriv) === False) { // Checks if a change is required
                        dbCommand("INSERT INTO privileges VALUES ('$user', '$PRIV')");
                        echo "Added $PRIV, ";
                        writeLog(10, "$user gained privilege $PRIV by $USERNAME or $address");
                    }
                } else {
                    if (array_search($PRIV, $oldPriv) !== False) { // Checks if a change is required
                        dbCommand("DELETE FROM privileges WHERE username='$user' AND privilege='$PRIV'");
                        echo "Removed $PRIV, ";
                        writeLog(10, "$user lost privilege $PRIV by $USERNAME or $address");
                    }
                }
            }
        }
        echo "saved";
    } else {
        missingPrivilege($USERNAME);
    }
}else {
    http_response_code(400);
    echo "Invalid command";
}
