<!DOCTYPE html>
<html>

<head>
    <title>
        Schaefer Family - Log Page
    </title>
    <?php
    $DESCRIPTION = "A place to manage the backups.";
    require_once '../include/all.php';
    ?>
</head>

<body>
    <?php
    require_once '../include/menu.php';
    echo "<div class='main'>";
    if (! $USERNAME) {
        echo "<h2>You are not logged in redirecting...</h2>";
        header("Refresh:3; url=/login.php", true);
        http_response_code(401);
    } elseif (!$PRIVILEGE["viewBackup"]) {
        http_response_code(403);
        header("Refresh:3; url=/index.php", true);
        echo "<h2>Forbidden redirecting...</h2>";
    } else {
        if (file_exists("../backups.json")) {
            echo "<h1>List of Backups</h1>";
            $backups = file_get_contents("../backups.json");
            $backups = json_decode($backups, true);
            foreach ($backups as $backup) {
                echo "<p>$backup</p>";
            }
        } else {
            echo "<h2>No backups exist you can restart the server to create one.";
        }
    }
    ?>
    </div>
</body>

</html>