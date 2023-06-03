<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
  <title>
    Klumpy
  </title>
  <?php
  $DESCRIPTION = "A block based game where you try to get the highest score you can while choosing cards from a deck.";
  require_once '../include/all.php';
  ?>
  <link rel="stylesheet" type="text/css" href="index.css?v=1.1.0" />
</head>

<body>
  <?php
  include '../include/menu.php';
  echo "<div class='main'>";
  ?>
  <h1>Klumpy</h1>
  <?php
  echo "<p>$DESCRIPTION</p>";
  if (!$USERNAME) {
    echo "<h2>You must be logged in to use the leaderboard and share your games.</h2>";
  }
  ?>
  <h3>Game Field</h3>
  <table id="game">
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
  <p>Color Clump Score<span title="All clumps of colors are added together by squaring the number of neighboring colors." style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="clump_score"></span></p>
  <p>Single Run Score<span title="The number of points based on the longest run of consecutive numbers in any orthogonal direction. Uses the following formula (9 - lowest_value) * (highest_value - lowest_value + 1)." style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="single_run_score"></span></p>
  <p>Increasing Row Score<span title="You get 10 points for every row where the numbers are strictly increasing." style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="increasing_row_across_score"></span></p>
  <p>Total Sum Score<span title="This is just a sum of all the numbers divided by 2." style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="tot_sum_scores"></span></p>
  <p>Unique Number Bonus<span title="A bonus of 50 points if you have all the numbers on the board" style="cursor: help" class="help ui-icon ui-icon-info"></span>: <span id="all_number_scores"></span></p>
  <p>Score: <span id="score"></span></p>
  <h3>Hand</h3>
  <table id="hand">
    <tr>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </table>
  <br>
  <button style="font-size: 50px" id="play">Play Card</button>
  <div id='winGame' class='popup'>
    <div class='popup-content'>
      <h1>You have finished!</h1>
      <p>You are in <span id='winGamePlace'></span> place.</p>
      <p>You have <span id='winGamePoints'></span> points.</p>
      <a href='/klumpy'><button>Restart</button></a>
      <a href="/klumpy/leaderboard"><button>View leaderboard</button></a>
      <a id="share" href="" style="display: none"><button>View Game and Share</button></a>
    </div>
  </div>
  </div>
  <script src="render.js?v=1.0.0"></script>
  <script src="score.js?v=1.0.0"></script>
  <script src="index.js?v=1.1.0"></script>
</body>

</html>