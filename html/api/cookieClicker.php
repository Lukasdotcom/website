<?php
require_once "api.php";
header("Access-Control-Allow-Origin: *");
if ($_POST["username"] !== null and $_POST["cookies"] !== null and $_POST["cookiesPs"] !== null and $_POST["room"] !== null) {
    $username = $_POST["username"];
    $cookies = $_POST["cookies"];
    $cookiesPs = $_POST["cookiesPs"];
    $room = $_POST["room"];
    $time = time();
    dbCommand("INSERT INTO cookieClicker VALUES ('$username', '$room', $cookies, $cookiesPs, $time)");
    dbCommand("DELETE FROM cookieClicker WHERE username='$username' AND room='$room' AND lastUpdate!='$time'");
    echo json_encode(dbRequest2("SELECT * FROM cookieClicker WHERE room='$room'"));
} else {
    http_response_code(400);
    echo "Invalid command";
}
