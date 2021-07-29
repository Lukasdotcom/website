<?php
require_once "api.php";
header("Access-Control-Allow-Origin: *");
if ($_POST["type"] === "view" and $_POST["username"] !== null and $_POST["cookies"] !== null and $_POST["cookiesPs"] !== null and $_POST["room"] !== null and $_POST["time"] !== null) {
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
    echo json_encode(["leaderboard" => dbRequest2("SELECT * FROM cookieClicker WHERE room='$room' ORDER BY cookiesPs DESC, cookies DESC"), "commands" => dbRequest2("SELECT * FROM cookieClickerCommand WHERE room='$room' AND username='$username'")]);
    dbCommand("DELETE FROM cookieClickerCommand WHERE room='$room' AND username='$username'");
} elseif ($_POST["type"] === "donate" and $_POST["username"] !== null and $_POST["room"] !== null and $_POST["cookies"] !== null) {
    $username = $_POST["username"];
    $cookies = floatval($_POST["cookies"]);
    $room = $_POST["room"];
    $sender = $_POST["sender"];
    $javascript = "Game.Earn($cookies); Game.Notify(`Donation`, `You were donated $cookies cookies by $sender.`, [10, 4])";
    dbCommand("INSERT INTO cookieClickerCommand (username, room, javascript) VALUES ('$username', '$room', '$javascript')");
} else {
    http_response_code(400);
    echo "Invalid command";
}
