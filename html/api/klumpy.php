<?php
require_once "api.php";
// Used to create an entry in the leaderboard
if (array_key_exists("board", $_POST) && array_key_exists("history", $_POST) && array_key_exists("points", $_POST) && array_key_exists("type", $_POST)) {
  exec("node ../klumpy/score.js " . escapeshellarg($OGPOST["board"]), $points);
  $points = array_sum(json_decode(join("", $points)));
  if ($points > 0 && $points == intval($_POST["points"]) && $_POST["type"] == "main") {
    if ($USERNAME) {
      $ID = random_int(0, 2147483600);
      dbCommand("DELETE FROM klumpy WHERE gameID = ?", [$ID]);
      dbCommand("INSERT INTO klumpy (gameID, `type`, username, score, board, history) VALUES (?, ?, ?, ?, ?, ?)", [$ID, $_POST["type"], $USERNAME, $points, $OGPOST["board"], $OGPOST["history"]]);
    }
    http_response_code(200);
    // Gets the leaderboard position
    $position = count(dbRequest2("SELECT DISTINCT(username) FROM klumpy WHERE score >= ? ORDER BY score", "*", [$points]));
    // Adds one to the leaderboard position if the user is not logged in due to that meaning that he is not in the DB
    if (!$USERNAME) {
      $position += 1;
      echo json_encode([
        "position" => $position,
        "points" => $points,
        "error" => true,
        "message" => "You are not logged in",
      ]);
    } else {
      echo json_encode([
        "position" => $position,
        "points" => $points,
        "error" => false,
        "gameID" => $ID,
      ]);
    }
  } else {
    http_response_code(400);
    echo "Invalid board";
  }
} elseif (array_key_exists("gameID", $_GET)) {
  $GAME = dbRequest2("SELECT * FROM klumpy WHERE gameID = ?", "*", [$_GET["gameID"]]);
  if ($GAME) {
    http_response_code(200);
    echo json_encode($GAME[0]);
  } else {
    http_response_code(404);
    echo "Invalid gameID";
  }
} elseif (array_key_exists("search", $_GET)) {
  $LIMIT = intval($_GET["search"]);
  $DATA = dbRequest2("SELECT gameID, username, score FROM klumpy ORDER BY score DESC LIMIT $LIMIT");
  echo json_encode($DATA);
} else {
  http_response_code(400);
  echo "Invalid command";
}
