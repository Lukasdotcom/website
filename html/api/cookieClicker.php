<?php
require_once "api.php";
header("Access-Control-Allow-Origin: *");
if (!array_key_exists("type", $_POST)) {
    $_POST["type"] = "";
}
if ($_POST["type"] === "view" and array_key_exists("username", $_POST) and array_key_exists("cookies", $_POST) and array_key_exists("cookiesPs", $_POST) and array_key_exists("room", $_POST) and array_key_exists("time", $_POST) and array_key_exists("powerOfCookies", $_POST) and array_key_exists("powerOfCookiesPs", $_POST)) {
    $username = $_POST["username"];
    $cookies = intval($_POST["cookies"]);
    $powerOfCookies = intval($_POST["powerOfCookies"]);
    $cookiesPs = intval($_POST["cookiesPs"]);
    $powerOfCookiesPs = intval($_POST["powerOfCookiesPs"]);
    $room = $_POST["room"];
    $time = $_POST["time"];
    if (dbRequest2("SELECT * FROM cookieClicker WHERE room='$room' AND username='$username'")) {
        dbCommand("UPDATE cookieClicker SET cookies = '$cookies', cookiesPs = '$cookiesPs', powerOfCookies = '$powerOfCookies', powerOfCookiesPs = '$powerOfCookiesPs', lastUpdate='$time' WHERE room='$room' AND username='$username';");
    } else {
        dbCommand("INSERT INTO cookieClicker (username, room, cookies, powerOfCookies, cookiesPs, powerOfCookiesPs, lastUpdate) VALUES ('$username', '$room', $cookies, $powerOfCookies, $cookiesPs, $powerOfCookiesPs, '$time')");
    }
    echo json_encode(["leaderboard" => dbRequest2("SELECT * FROM cookieClicker WHERE room='$room' ORDER BY cookiesPs DESC, cookies DESC")]);
} else {
    http_response_code(400);
    echo "Invalid command";
}
