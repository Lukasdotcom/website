<?php
// Will be retired soon in exchange for the api folder to improve performance and readability
header('Content-Type: application/json');
if($_GET["key"]) { // Will check if a get key is given and then will use the key as the cookie variable
    $_COOKIE["user"] = $_GET["key"];
} else {
    $_COOKIE["user"] = $_POST["key"];
}
include 'include/functions.php';
function missingPrivilege($USERNAME) {
    if ($USERNAME) {
        http_response_code(403);
        echo "Forbidden";
    } else {
        http_response_code(401);
        echo "Not logged in";
    }
}
if($_GET["internet"] == "data") { // Will give internet data
    $data = dbRequest2("SELECT * FROM internet ORDER BY id Asc");
    echo json_encode($data);
} elseif ($_GET["internet"] === "button"){
    echo "Unsupported. This message will eventually be removed";
    http_response_code(410);
} elseif ($_POST["internet"] === "edit") { // Will edit the internet data in the database
    if ($PRIVILEGE["internet"]) {
        $id = intval($_POST["id"]);
        $startHour = intval($_POST["startHour"]);
        $startMinute = intval($_POST["startMinute"]);
        $endHour = intval($_POST["endHour"]);
        $endMinute = intval($_POST["endMinute"]);
        $expire = intval($_POST["expire"]);
        if (dbRequest2("SELECT * FROM internet WHERE id='$id'")) {
            dbEdit("internet", [["hour", $startHour], ["minute", $startMinute], ["hour2", $endHour], ["minute2", $endMinute], ["expire", $expire]], ["id", $id], 0);
            $startTime = $startHour . ":" . $startMinute;
            $endTime = $endHour . ":" . $endMinute;
            writeLog(11, "User $USERNAME changed internet schedule entry number $id to contents, from $startTime to $endTime with expiration of $expire.");
        } else {
            dbAdd([$startHour, $startMinute, $endHour, $endMinute, $expire, $id], "internet");
            $startTime = $startHour . ":" . $startMinute;
            $endTime = $endHour . ":" . $endMinute;
            writeLog(11, "User $USERNAME added internet schedule entry number $id with contents from $startTime to $endTime with expiration of $expire.");
        }
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_POST["internet"] === "delete") { // Will delete data from internet schedule
    if ($PRIVILEGE["internet"]) {
        $id = intval($_POST["id"]);
        $input = dbRequest2("SELECT * FROM internet WHERE id='$id'");
        dbCommand("DELETE FROM internet WHERE id='$id'");
        $startTime = $input[0]["hour"] . ":" . $input[0]["minute"];
        $endTime = $input[0]["hour2"] . ":" . $input[0]["minute2"];
        $expire = $input[0]["expire"];
        writeLog(11, "User $USERNAME deleted internet schedule entry number $id with contents from $startTime to $endTime with expiration of $expire.");
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_POST["login"]) { // Used to login or signup and get the cookie
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
    if ($_POST["login"] === "signup") { // Will check if a signup was done
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
    $RESULT = $RESULT[0];
    if ($RESULT and password_needs_rehash($RESULT, PASSWORD_BCRYPT)) {
        $RESULT2 = $RESULT;
        $RESULT = password_hash($RESULT, PASSWORD_BCRYPT);
        dbEdit("users", [["password", $RESULT]], ["password", $RESULT2], 0);
    }
    if (password_verify($_POST["password"], $RESULT)) {
        // If the username and password are valid a cookie entry is put into the database and the cookie is put on the user
        $USERNAME = $_POST["username"];
        // logs the fact that a login happened and if a signup happened
        if ($_POST["login"] == "signup") {
            writeLog(2, "$USERNAME created by $address");
        }
        writeLog(0, "$USERNAME was logged in by $address");
        $Time = time() + 3600*12;
        $Cookie = $USERNAME;
        $Cookie .= microtime();
        $Cookie = sanitize(substr(sha1($Cookie), 5));
        $CookieForDB = [$Cookie, $USERNAME, $Time];
        dbAdd($CookieForDB, "cookies");
        setcookie("user", $Cookie, time() + 600, "/");
        echo json_encode($Cookie);
    } else {
        http_response_code(401);
        echo "Wrong password or username try again.";
    }
} elseif ($_GET["log"] == "data"){ // Gets the log
    if ($PRIVILEGE["viewLog"]) {
        echo json_encode(array_reverse(dbRequest("*", "log", "", "", 2)));
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_POST["log"] == "remove") { // To delete an entry in log
    if ($PRIVILEGE["deleteLog"]) {
        $possibleDelete = dbRequest("message", "log", "time", $_POST["time"], 0);
        if (array_search($OGPOST["message"], $possibleDelete) !== NULL and array_search($OGPOST["message"], $possibleDelete) !== false) {
            dbRemove("log", ["message", "time"], [$OGPOST["message"], $_POST["time"]], 0);
        }
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_POST["server"] == "restart") { // To delete an entry in log
    if ($PRIVILEGE["restartServer"]) {
        echo "Restarting";
        $restart = fopen("restart.json", "w");
        fclose($restart);
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_POST["server"] == "update") { // To delete an entry in log
    if ($PRIVILEGE["updateServer"]) {
        echo "Updating.";
        $update = fopen("update.json", "w");
        fclose($update);
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}