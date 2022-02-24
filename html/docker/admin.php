<!DOCTYPE html>

<html dir="ltr" lang="en">

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
        <h1>Images</h1>
        <table>
            <tbody id='image'>
                <tr>
                    <th>Docker Image</th><th>Name</th>
                </tr>
            </tbody>
        </table> 
        <br>
        <label for="imageName">Image: </label><input id='imageName'>
        <br>
        <label for="name">Easy name: </label><input id='name'>
        <br>
        <button id='createImage'>Create</button>
        <h3>How to create Image?</h3>
        <p>All you have to do to create a valid image for this is have a docker image that accepts an enviromental variable called VNC_PASSWD that is the password for the web interface and that exposes port 80 as a way to access the web interface. Then just publish it on dockerhub and pull it onto the device you are running this on.</p>
        <?php
    }
    ?>
    </div>
</body>

</html>