<?php
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
if($_GET["internet"] == "data") {
    $data = dbRequest2("SELECT * FROM internet ORDER BY id Asc");
    echo json_encode($data);
} elseif ($_GET["internet"] === "button"){
    echo "Unsupported. This message will eventually be removed";
    http_response_code(410);
} elseif ($_POST["internet"] === "edit") {
    if ($PRIVILEGE["internet"]) {
        $id = intval($_POST["id"]);
        $startHour = intval($_POST["startHour"]);
        $startMinute = intval($_POST["startMinute"]);
        $endHour = intval($_POST["endHour"]);
        $endMinute = intval($_POST["endMinute"]);
        $expire = intval($_POST["expire"]);
        if (dbRequest2("SELECT * FROM internet WHERE id='$id'")) {
            dbEdit("internet", [["hour", $startHour], ["minute", $startMinute], ["hour2", $endHour], ["minute2", $endMinute], ["expire", $expire]], ["id", $id], 0);
            writeLog(11, "User $USERNAME changed internet schedule entry number $id");
        } else {
            dbAdd([$startHour, $startMinute, $endHour, $endMinute, $expire, $id], "internet");
            writeLog(11, "User $USERNAME added internet schedule entry number $id");
        }
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_POST["internet"] === "delete") {
    if ($PRIVILEGE["internet"]) {
        $id = intval($_POST["id"]);
        dbCommand("DELETE FROM internet WHERE id='$id'");
        writeLog(11, "User $USERNAME deleted internet schedule entry number $id");
    } else {
        missingPrivilege($USERNAME);
    }
} elseif ($_POST["login"]) { // Used to login or signup and get the cookie
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
    for ($i=0; $i<10; $i++) {
        dbAdd([$address, time()], "requests");
    }
    dbAdd([$address, time()], "requests");
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
        if ($_POST["signup"]) {
            writeLog(2, "$USERNAME created by $address");
        }
        writeLog(0, "$USERNAME was logged in by $address");
        $Time = time() + 3600;
        $Cookie = $USERNAME;
        $Cookie .= $Time;
        $Cookie = sanitize(substr(password_hash($_POST["password"], PASSWORD_BCRYPT), 15));
        $CookieForDB = [$Cookie, $USERNAME, $Time];
        dbAdd($CookieForDB, "cookies");
        setcookie("user", $Cookie, time() + 600, "/");
        echo json_encode($Cookie);
    } else {
        http_response_code(401);
        echo "Wrong password or username try again.";
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}