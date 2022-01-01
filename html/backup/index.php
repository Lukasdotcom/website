<!DOCTYPE html>
<html>

<head>
    <title>
        Schaefer Family - Backups
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
            if (array_key_exists("restore", $OGPOST)) { // Used to restore a backup for the server
                if ($PRIVILEGE["restore"]) {
                    $backups = file_get_contents("../updateInfo.log");
                    if (array_search($backups, $OGPOST["restore"])  !== false) { // Makes sure that the file actually exists
                        $restart = fopen("../restore.json", "w");
                        $restore = $OGPOST["restore"];
                        fwrite($restart, json_encode($restore));
                        fclose($restart);
                        $restore = htmlspecialchars($restore);
                        echo "Restoring file -> $restore";
                    } else {
                        echo "Backup does not exist";
                        http_response_code(404);
                    }
                } else {
                    missingPrivilege($USERNAME);
                }
            } 
            $backups = file_get_contents("../backups.json");
            $backups = json_decode($backups, true);
            echo "<table><th>File Name</th>";
            foreach ($backups as $backup) { // Lists all the backups on the server
                echo "<tr>
                    <td>$backup</td>
                    <td>
                        <form method='post' action='/backup/'>
                            <button name='restore' value='$backup' type='submit'>
                                Restore
                            </button>
                        </form>
                    </td>
                    </tr>";
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