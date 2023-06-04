<?php
// Makes sure that this was a valid game
function valid_moves($board, $history)
{
  if (count($board) != 4 && count($history) != 16) {
    return false;
  }
  foreach ($board as $row) {
    if (count($row) != 4) {
      return false;
    }
  }
  $rounds = pow(2, 16) - 1;
  foreach ($history as $round) {
    if (count($round["hand"]) != 3) {
      return false;
    }
    if (count($round["board"]) != 2) {
      return false;
    }
    $rounds -= pow(2, $round["board"][0] + $round["board"][1] * 4);
    if ($rounds < 0) {
      return false;
    }
    $board_card = $board[$round["board"][0]][$round["board"][1]];
    $hand_card1_match = $round["hand"][0]["number"] == $board_card["number"] && $round["hand"][0]["color"] == $board_card["color"];
    $hand_card2_match = $round["hand"][1]["number"] == $board_card["number"] && $round["hand"][1]["color"] == $board_card["color"];
    $hand_card3_match = $round["hand"][2]["number"] == $board_card["number"] && $round["hand"][2]["color"] == $board_card["color"];
    if (!$hand_card1_match && !$hand_card2_match && !$hand_card3_match) {
      return false;
    }
  }
  return $rounds == 0;
}
require_once "api.php";
// Used to create an entry in the leaderboard
if (array_key_exists("board", $_POST) && array_key_exists("history", $_POST) && array_key_exists("points", $_POST) && array_key_exists("type", $_POST)) {
  exec("node ../klumpy/score.js " . escapeshellarg($OGPOST["board"]), $points);
  $points = array_sum(json_decode(join("", $points)));
  if ($points > 0 && $points == intval($_POST["points"]) && $_POST["type"] == "main" && valid_moves(json_decode($OGPOST["board"], true), json_decode($OGPOST["history"], true))) {
    if ($USERNAME) {
      dbCommand("INSERT INTO klumpy (`type`, username, score, board, history) VALUES (?, ?, ?, ?, ?)", [$_POST["type"], $USERNAME, $points, $OGPOST["board"], $OGPOST["history"]]);
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
      $ID = dbRequest2("SELECT gameID FROM klumpy WHERE username=? AND board=? AND history=?", "gameID", [$USERNAME, $OGPOST["board"], $OGPOST["history"]])[0];
      writeLog(31, "$USERNAME scored $points on leaderboard for position $position");
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
