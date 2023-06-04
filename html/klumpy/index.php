<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
  <title>
    Klumpy
  </title>
  <?php
  $DESCRIPTION = "Klumpy is a fun single-player game where players try to maximize their points by strategically placing their cards in a 4x4 grid. Each turn a player will have the option of three cards to place on any open square of the board.";
  require_once '../include/all.php';
  ?>
  <link rel="stylesheet" type="text/css" href="index.css?v=2.0.0" />
</head>

<body>
  <?php
  include '../include/menu.php';
  echo "<div class='main'>";
  ?>
  <h1>Klumpy</h1>
  <h3>Rules</h3>
  <?php
  echo "<p>$DESCRIPTION <a href='/klumpy/leaderboard.php'>There is also a leaderboard you can look at here.</a></p>";
  ?>
  <?php
  if (!$USERNAME) {
    echo "<h2>You must be logged in to submit your score to the leaderboard and share your games. <a href='/login.php'>Login here</a></h2>";
  }
  ?>
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
  <h3>Hand &nbsp;<button style="font-size: 30px" id="play">Play Card</button></h3>
  <table class="game" id="hand">
    <tr>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </table>
  <p>Total Score: <span id="score"></span></p>
  <p>Color Clump Score<span title="All clumps of colors are added together by squaring the number of neighboring colors." style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="clump_score"></span></p>
  <p>Single Run Score<span title="The number of points based on the longest run of consecutive numbers in any orthogonal direction. Uses the following formula (9 - lowest_value) * (highest_value - lowest_value + 1)." style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="single_run_score"></span></p>
  <p>Increasing Row Score<span title="You get 10 points for every row where the numbers are strictly increasing." style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="increasing_row_across_score"></span></p>
  <p>Total Sum Score<span title="This is just a sum of all the numbers divided by 2." style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="tot_sum_scores"></span></p>
  <p>Unique Number Bonus<span title="A bonus of 50 points if you have all the numbers on the board" style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="all_number_scores"></span></p>
  <br>
  <div id='winGame' class='popup'>
    <div class='popup-content'>
      <h1>You have finished!</h1>
      <p>You are number <span id='winGamePlace'></span> on the leaderboard.</p>
      <p>Score: <span id='winGamePoints'></span></p>
      <a href='/klumpy'><button>Restart</button></a>
      <a href="/klumpy/leaderboard.php"><button>View leaderboard</button></a>
      <a id="share" href="" style="display: none"><button>View Game and Share</button></a>
      <h2 id="error"></h2>
    </div>
  </div>
  </div>
  <script src="render.js?v=1.0.0"></script>
  <script src="score.js?v=1.0.0"></script>
  <script src="index.js?v=1.2.0"></script>
</body>

</html>