<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
    <title>
        Schaefer Family - Manage Sessions
    </title>
    <?php
    $DESCRIPTION = "A place where you can manage your sessions and api keys";
    require_once '../include/all.php';
    ?>
</head>

<body>
    <?php
    include '../include/menu.php';
    echo "<div class='main'>";
    if (! $USERNAME) { // Checks that the user is logged in or if the new user or login information is valid
        header("Refresh:3; url=/login.php", true);
        echo "<h2>You are not logged in redirecting...</h2>";
    } else {
        ?>
        <script type='text/javascript' src='key.js'></script>
        <h1>Session Manager</h1>
        <table>
            <tbody id='keys'>
                <tr>
                    <th>Key</th><th>Previous IP</th><th>Expiration</th>
                </tr>
            </tbody>
        </table>
        <h3>Create Session or api key below here:</h3>
        <div>
            <label for="expire">Expiration(0 means that it will never expire): </label><input type='number' value=<?php echo time();?> id="expire" style="width: 100px;"><br>
            <button id='create'>Create</button>
        </div>
        <?php
    }
    ?>
    </div>
</body>

</html>