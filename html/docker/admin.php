<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family - Manage Containers
    </title>
    <?php
    $DESCRIPTION = "A way to manage the docker containers and which ones can be used.";
    require_once '../include/all.php';
    ?>
</head>

<body>
    <?php
    include '../include/menu.php';
    echo "<div class='main'>";
    if (! $USERNAME) {
        echo "<h2>You are not logged in redirecting...</h2>";
        header("Refresh:3; url=/login.php", true);
        http_response_code(401);
    } else if (! $PRIVILEGE["dockerAdmin"]) {
        http_response_code(403);
        header("Refresh:3; url=/index.php", true);
        echo "<h2>Forbidden redirecting...</h2>";
    } else {
        $images = dbRequest2("SELECT * FROM dockerImages");
        echo "<script>var images=JSON.parse('";
        echo json_encode($images);
        echo "')</script>";
        ?>
        <script type='text/javascript' src='admin.js'></script>
        <h1>Containers</h1>
        <table>
            <tbody id='container'>
                <tr>
                    <th>ID</th><th>Status</th><th>Link</th><th>Port</th>
                </tr>
            </tbody>
        </table> 
        <br>
        <label for="link">Link for the server: </label><input id='link'>
        <br>
        <label for="port">The port to be used: </label><input type='number' id='port'>
        <br>
        <button id='createContainer'>Create</button>
        <?php
    }
    ?>
    </div>
</body>

</html>