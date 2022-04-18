<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <title>
        Backups
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
        if (file_exists("../backups.json")) { // Checks if the server can see any backups
            echo "<h1>List of Backups</h1>";
            if (! array_key_exists("key", $_POST)) {
                $_POST["key"] = "";
            }
            if (array_key_exists("restore", $OGPOST)) { // Used to restore a backup for the server
                if ($PRIVILEGE["restore"] and $_POST["key"] === $_COOKIE["user"]) {
                    $backups = json_decode(file_get_contents("../backups.json"));
                    if (array_search($OGPOST["restore"], $backups)  !== false) { // Makes sure that the file actually exists
                        $restart = fopen("../restore.json", "w");
                        $restore = $OGPOST["restore"];
                        fwrite($restart, json_encode($restore));
                        fclose($restart);
                        $restore = htmlspecialchars($restore);
                        echo "<p>Restoring backup -> $restore</p>";
                    } elseif ($OGPOST["restore"] === "latest") { // Checks if it is just the latest
                        $restart = fopen("../restore.json", "w");
                        fwrite($restart, '"latest"');
                        fclose($restart);
                        echo "<p>Restoring latest backup</p>";
                    } else {
                        echo "<p>Backup does not exist</p>";
                    }
                } else {
                    echo "<script>alert('You can not restore a backup')</script>";
                }
            } 
            $backups = file_get_contents("../backups.json"); // Gets list of backups
            $backups = json_decode($backups, true);
            $key = $_COOKIE["user"];
            if ($PRIVILEGE["restore"]) {
                # Used to restore the latest backup
                echo "
                <form method='post' action='/backup/'>
                    <input type='hidden' name='key' value='$key'>
                    <button name='restore' value='latest' type='submit'>
                        Restore Newest Backup
                    </button>
                </form>";
            }
            echo "<table><th>File Name</th>";
            foreach ($backups as $backup) { // Lists all the backups on the server
                echo "<tr>
                    <td>$backup</td>";
                if ($PRIVILEGE["restore"]) {
                    echo "<td>
                        <form method='post' action='/backup/'>
                            <input type='hidden' name='key' value='$key'>
                            <button name='restore' value='$backup' type='submit'>
                                Restore
                            </button>
                        </form>
                    </td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<h2>No backups exist you can restart the server to create one.";
        }
    }
    ?>
    </div>
</body>

</html>