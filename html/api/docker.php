<?php
require_once "api.php";
if (! $PRIVILEGE["docker"] and ! $PRIVILEGE["dockerAdmin"]) { // Makes sure that the person has the right privilege
    missingPrivilege($USERNAME);
    exit();
}
if (array_key_exists("containers", $_GET)) { // Will list all avaliable containers
    echo json_encode(dbRequest2("SELECT * FROM docker WHERE owner='$USERNAME' or action='stopped'"));
} else if (array_key_exists("start", $_POST) and array_key_exists("image", $_POST)) { // Used to start a container and will return the password
    $id = $_POST["start"];
    $image = $OGPOST["image"];
    if (! dbRequest2("SELECT * FROM docker WHERE action='stopped' and ID='$id'")) { // Will check if the container can be started
        echo "Container does not exist or is used";
        http_response_code(404);
    } elseif (! dbRequest2("SELECT * FROM dockerImages WHERE realName=?", "*", [$image])) { // Will cheeck if the image exists
        echo "Image does not exist";
        http_response_code(404);
    } else { // Will start the image
        $password = $USERNAME;
        $password .= rand();
        $password = sanitize(substr(sha1($password), 5, 8));
        dbCommand("UPDATE docker SET action='starting', image='$image', password='$password' WHERE ID='$id'");
        writeLog(23, "$USERNAME is starting container with $image image and id $id and an IP of $address");
        echo $password;
    }
} else if (array_key_exists("images", $_GET)) {
    echo json_encode(dbRequest2("SELECT * FROM dockerImages")); // Will list all available images
} else {
    http_response_code(400);
    echo "Invalid command";
}