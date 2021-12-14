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
        <p>Join Game below here</p>
        <table id='games'>
        </table>
    <?php
    }
    ?>
    </div>
</body>

</html>
