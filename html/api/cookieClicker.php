<?php
require_once "api.php";
header("Access-Control-Allow-Origin: *");
if ($_POST["username"] !== null and $_POST["cookies"] !== null and $_POST["cookiesPs"] !== null and $_POST["room"] !== null) {
    $username = $_POST["username"];
    $cookies = $_POST["cookies"];
    $cookiesPs = $_POST["cookiesPs"];
    $room = $_POST["room"];
    $time = time();
    if (dbRequest2("SELECT * FROM cookieClicker WHERE room='$room' AND username='$username'")) {
        dbCommand("UPDATE cookieClicker SET cookies = '$cookies', cookiesPerSecond = '$cookiesPs', lastUpdate='$time' WHERE room='$room' AND username='$username';");
    } else {
        dbCommand("INSERT INTO cookieClicker (username, room, cookies, cookiesPerSecond, lastUpdate) VALUES ('$username', '$room', $cookies, $cookiesPs, $time)");
    }
    echo json_encode(dbRequest2("SELECT * FROM cookieClicker WHERE room='$room'"));
} else {
    http_response_code(400);
    echo "Invalid command";
}
