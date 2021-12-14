<!DOCTYPE html>

<html>

<head>
    <title>
        Golf
    </title>
    <?php
    $DESCRIPTION = "A way to play the card game golf online with your friends.";
    require_once '../include/all.php';
    ?>
    <script type='text/javascript' src='/javascript/functions.js'></script>
    <script type='text/javascript' src='index.js'></script>
</head>

<body>
    <?php
    require_once '../include/menu.php';
    echo "<div class='main'>";
    if (! $USERNAME) {
        echo "<h2>You are not logged in redirecting...</h2>";
        header("Refresh:3; url=/login.php?redirect=golf", true);
        http_response_code(401);
    } else {
        ?>
        <h1>Golf</h1>
        <p>Join Game below here:</p>
        <table id='games'>
        </table>
        <p>Create Game below here:</p>
        <div>
            <label for="name">Name: </label><input type='text' value='' id="name"><br>
            <label for="cardNumber">Cards: </label><input type='number' value='6' id="cardNumber"><br>
            <label for="flipNumber">Flipped Cards at Start: </label><input type='number' value='2' id="flipNumber"><br>
            <label for="playersToStart">Players: </label><input type='number' value='4' id="playersToStart"><br>
            <label for="password">Password(leave blank for none): </label><input type='password' value='' id="password"><br>
            <button id='create'>Create</button>
        </div>
    <?php
    }
    ?>
    </div>
</body>

</html>
