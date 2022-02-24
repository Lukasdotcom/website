<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
    <title>
        Schaefer Family - Containers
    </title>
    <?php
    $DESCRIPTION = "A way to start a few VM and access them in a web browser";
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
    } else if (! $PRIVILEGE["docker"]) {
        http_response_code(403);
        header("Refresh:3; url=/index.php", true);
        echo "<h2>Forbidden redirecting...</h2>";
    } else {
        $images = dbRequest2("SELECT * FROM dockerImages");
        echo "<script>var images=JSON.parse('";
        echo json_encode($images);
        echo "')</script>";
        ?>
        <script type='text/javascript' src='index.js'></script>
        <h1>Run Containers</h1>
        <table>
            <tbody id='docker'>
                <tr>
                    <th>ID</th><th>Status</th><th>Image</th><th>Password</th><th>Link</th>
                </tr>
            </tbody>
        </table> 
        <?php
    }
    ?>
    </div>
</body>

</html>