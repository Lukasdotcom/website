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
  echo "<p>$DESCRIPTION</p>";
  ?>
  <h3>Calculating Points</h3>
  <ol>
    <li>
      Having “clumps” of cards of the same color. Each clump is worth its area squared in points (i.e one large clump is more valuable than two smaller ones)
    </li>
    <li>
      Having a large chain of consecutive numbers. The numbers must be connected orthogonally (i.e diagonals don't count). Points will be awarded based on the length of this chain and the lowest number within in. (Note: While a chain that goes 1-2 may be worth more points than one that goes 4-5-6, your longest chain will be the only one scored. In case of a tie of length, the more valuable chain is score).
    </li>
    <li>
      Having strict-increasing rows of numbers. At the end of the game, the number of strictly-increasing rows will be counted and scored according to the following: 1 row-10 points, 2-rows-25 points, 3 rows-45 points, 4-rows 70 points.
    </li>
    <li>
      Having large numbers. The sum of every card will be totaled and divided by two (rounded up). This will then be added to your total score.
    </li>
    <li>
      Having a variety of numbers. Players get the number of unique cards squared in points.
    </li>
  </ol>
  <p><a href='/klumpy/leaderboard.php'>Click here for the leaderboard.</a></p>
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
  <p>Color Clump Score: <span id="clump_score"></span></p>
  <p>Single Run Score: <span id="single_run_score"></span></p>
  <p>Increasing Row Score: <span id="increasing_row_across_score"></span></p>
  <p>Total Sum Score: <span id="tot_sum_scores"></span></p>
  <p>Unique Number Bonus: <span id="all_number_scores"></span></p>
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
  <script src="render.js?v=1.0.1"></script>
  <script src="score.js?v=1.0.0"></script>
  <script src="index.js?v=1.3.0"></script>
</body>

</html>