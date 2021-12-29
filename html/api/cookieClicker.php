<?php
require_once "api.php";
header("Access-Control-Allow-Origin: *");
if (!array_key_exists("type", $_POST)) {
    $_POST["type"] = "";
}
if ($_POST["type"] === "view" and array_key_exists("username", $_POST) and array_key_exists("cookies", $_POST) and array_key_exists("cookiesPs", $_POST) and array_key_exists("room", $_POST) and array_key_exists("time", $_POST)) {
    $username = $_POST["username"];
    $cookies = floatval($_POST["cookies"]);
    $cookiesPs = floatval($_POST["cookiesPs"]);
    $room = $_POST["room"];
    $time = $_POST["time"];
    if (dbRequest2("SELECT * FROM cookieClicker WHERE room='$room' AND username='$username'")) {
        dbCommand("UPDATE cookieClicker SET cookies = '$cookies', cookiesPs = '$cookiesPs', lastUpdate='$time' WHERE room='$room' AND username='$username';");
    } else {
        dbCommand("INSERT INTO cookieClicker (username, room, cookies, cookiesPs, lastUpdate) VALUES ('$username', '$room', $cookies, $cookiesPs, '$time')");
    }
    echo json_encode(["leaderboard" => dbRequest2("SELECT * FROM cookieClicker WHERE room='$room' ORDER BY cookiesPs DESC, cookies DESC")]);
} else {
    http_response_code(400);
    echo "Invalid command";
}
