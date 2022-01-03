<?php
require_once "api.php";
/**
 * @param array the user you want to search for if this is empty the logged in user is used. It will check the user in that array.
 * @return string of the user or will quit if the user does not exist
 */
function checkUser($array) {
    global $USERNAME;
    if (array_key_exists("user", $array)) {
        $user = $array["user"];
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
if (array_key_value("type", $_GET, "view")) { // Will return all privileges the user has in a list
    $user = checkUser($_GET);
    if ($PRIVILEGE["editUser"] or ($USERNAME === $user and $USERNAME)) {
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
} elseif (array_key_value("type", $_POST, "edit")) { // Used to edit the users privileges
    $user = checkUser($_POST);
    if ($PRIVILEGE["editUser"] or ($USERNAME === $user and $USERNAME)) {
        // Gets the previous privileges
        $oldPriv = dbRequest2("SELECT privilege FROM privileges WHERE username='$user'", "privilege");
        if (gettype($oldPriv) !== "array") { // Will make sure oldPriv is an array
            $oldPriv = [];
        }
        // Goes through every privilege the user has and sees what the user wants on the new user they are editing
        foreach($PRIVILEGE as $PRIV => $bool) {
            if ($bool){
                if ($_POST[$PRIV] and $_POST[$PRIV] !== "false" and $_POST[$PRIV] !== "False") {
                    if (array_search($PRIV, $oldPriv) === false) { // Checks if a change is required
                        dbCommand("INSERT INTO privileges VALUES ('$user', '$PRIV')");
                        echo "Added $PRIV, ";
                        writeLog(10, "$user gained privilege $PRIV by $USERNAME or $address");
                    }
                } else {
                    if (array_search($PRIV, $oldPriv) !== false) { // Checks if a change is required
                        dbCommand("DELETE FROM privileges WHERE username='$user' AND privilege='$PRIV'");
                        echo "Removed $PRIV, ";
                        writeLog(10, "$user lost privilege $PRIV by $USERNAME or $address");
                    }
                }
            }
        }
        echo "Saved";
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_value("type", $_POST, "password")) { // Used to change password
    $user = checkUser($_POST);
    if ($USERNAME == $user or $PRIVILEGE["changeCredintials"]) {
        $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
        dbCommand("UPDATE users SET password = '$password' WHERE username='$user';");
        echo "Saved new password for $user.";
        writeLog(3, "$user's password was changed by $USERNAME or $address");
    } else {
        missingPrivilege($USERNAME);
    }
} elseif (array_key_value("type", $_POST, "delete")) { // Used to delete a user
    $user = checkUser($_POST);
    if ($USERNAME == $user or $PRIVILEGE["deleteUser"]) {
        dbCommand("DELETE FROM users WHERE username = '$user';");
        dbCommand("DELETE FROM localStorage WHERE username = '$user';");
        dbCommand("DELETE FROM cookies WHERE username = '$user';");
        dbCommand("DELETE FROM space3likes WHERE account = '$user';");
        dbCommand("DELETE FROM space3 WHERE owner = '$user';");
        dbCommand("DELETE FROM privileges WHERE username = '$user';");
        echo "Deleted user $user.";
        if ($USERNAME == $user) {
            writeLog(1, "$user deleted their own user with ip of $address");
        } else {
            writeLog(1, "$user was deleted by $USERNAME or $address");
        }
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}
