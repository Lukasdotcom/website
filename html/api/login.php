<?php
require_once "api.php";
if (array_key_exists("username", $_POST) and array_key_exists("type", $_POST) and array_key_exists("password", $_POST)) { // Used to login or signup and get the cookie
    header("Access-Control-Allow-Origin: *"); // Will allow it from any origin to allow for space 3 to work in any domain
    // Will check if the ip address has passed its throttle point
    $jsonInfo = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/config.json");
    $jsonData = json_decode($jsonInfo, true);
    $expireRequests = time() - $jsonData["throttleTime"];
    dbCommand("DELETE FROM requests WHERE time<'$expireRequests'");
    $requests = dbRequest2("SELECT * FROM requests WHERE ip='$address'");
    dbAdd([$address, time()], "requests");
    if (sizeof($requests) > $jsonData["throttle"]) {
        echo "Too many login attempts.";
        http_response_code(429);
        exit();
    }
    if ($_POST["type"] === "signup") { // Will check if a signup was done
        $RESULT = dbRequest("username", "users", "username", $_POST["username"], 0);
        if ($RESULT == False) {
            $PASSWORD = password_hash($_POST["password"], PASSWORD_BCRYPT);
            dbAdd([$_POST["username"], $PASSWORD], "users");
        } else {
            http_response_code(409);
            $USER = $_POST["username"];
            echo "User $USER already exists.";
            exit();
        }
    }
    $RESULT = dbRequest("password", "users", "username", $_POST["username"], 0);
    if ($RESULT) {
        $RESULT = $RESULT[0];
        if (password_needs_rehash($RESULT, PASSWORD_BCRYPT)) {
            $RESULT2 = $RESULT;
            $RESULT = password_hash($RESULT, PASSWORD_BCRYPT);
            dbEdit("users", [["password", $RESULT]], ["password", $RESULT2], 0);
        }
        if (password_verify($_POST["password"], $RESULT)) {
            // If the username and password are valid a cookie entry is put into the database and the cookie is put on the user
            $USERNAME = $_POST["username"];
            // logs the fact that a login happened and if a signup happened
            if ($_POST["type"] == "signup") {
                writeLog(2, "$USERNAME created by $address");
            }
            writeLog(0, "$USERNAME was logged in by $address");
            $Time = time() + 3600*12;
            $Cookie = $USERNAME;
            $Cookie .= rand();
            $Cookie = sanitize(substr(sha1($Cookie), 5));
            $CookieForDB = [$Cookie, $USERNAME, $Time];
            dbAdd($CookieForDB, "cookies");
            setcookie("user", $Cookie, time() + 600, "/");
            echo json_encode($Cookie);
        } else {
            http_response_code(401);
            echo "Wrong password";
        }
    } else {
        http_response_code(401);
        echo "User does not exist";
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}