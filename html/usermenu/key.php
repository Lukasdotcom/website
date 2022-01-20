<!DOCTYPE html>

<html>

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
                    <th>Key</th><th>Expiration</th>
                </tr>
            </tbody>
        </table>
        <?php
    }
    ?>
    </div>
</body>

</html>