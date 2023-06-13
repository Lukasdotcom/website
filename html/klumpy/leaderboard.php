<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
  <title>
    Klumpy Leaderboard
  </title>
  <?php
  $DESCRIPTION = "A Leaderboard for Klumpy where you can see all the games and view them.";
  require_once '../include/all.php';
  ?>
  <link rel="stylesheet" type="text/css" href="index.css?v=2.0.0" />
</head>

<body>
  <?php
  include '../include/menu.php';
  echo "<div class='main''>";
  ?>
  <h1>Klumpy Leaderboard</h1>
  <input onclick="loadMore()" type='checkbox' id='show_all' name='show_all'>
  <label for='show_all'>Check this to show all games</label><br>
  <table class="leaderboard" id="leaderboard">
    <th>Position</th>
    <th>Name</th>
    <th>Score</th>
    <th>View</th>
  </table>
  <button onclick="loadMore()">Load More</button>
  <div id='historicalGame' class='popup'>
    <div class='popup-content'>
      <button onclick="close_historicalGame()">Close</button>
      <h1>Game from <span id='username'></span></h1>
      <p>Slide this to go through the game</p>
      <div id="slider"></div>
      <h3>Game Field</h3>
      <table class="game" id="game">
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </table>
      <p>Color Clump Score: <span id="clump_score"></span></p>
      <p>Single Run Score: <span id="single_run_score"></span></p>
      <p>Increasing Row Score: <span id="increasing_row_across_score"></span></p>
      <p>Total Sum Score: <span id="tot_sum_scores"></span></p>
      <p>Unique Color Bonus: <span id="all_number_scores"></span></p>
      <p>Score: <span id="score"></span></p>
      <h3>Hand</h3>
      <table class="game" id="hand">
        <tr>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </table>
    </div>
  </div>
  </div>
  <script src="render.js?v=1.1.0"></script>
  <script src="score.js?v=1.1.0"></script>
  <script src="leaderboard.js?v=1.0.2"></script>
</body>

</html>