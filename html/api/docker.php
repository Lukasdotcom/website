<?php
require_once "api.php";
if (! $PRIVILEGE["docker"] and ! $PRIVILEGE["dockerAdmin"]) { // Makes sure that the person has the right privilege
    missingPrivilege($USERNAME);
    exit();
}
if (array_key_exists("containers", $_GET)) { // Will list all avaliable containers
    if ($PRIVILEGE["dockerAdmin"] and $_POST["all"]) { // Allows for all containers to be returned if that is requested
        echo json_encode(dbRequest2("SELECT * FROM docker"));
    } else {
        echo json_encode(dbRequest2("SELECT * FROM docker WHERE owner='$USERNAME' or action='stopped'")); 
    }
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
} else if (array_key_exists("deleteContainer", $_POST)) { // Used to delete a container
    $id = $_POST["deleteContainer"];
    if ($PRIVILEGE["dockerAdmin"]) {
        if (dbRequest2("SELECT * FROM docker WHERE ID='$id'")) {
            dbCommand("DELETE FROM docker WHERE ID='$id'");
            writeLog(28, "$USERNAME deleted container with id $id and with ip $address");
            echo "Deleted container";
        } else {
            http_response_code(404);
            echo "Could not find container";
        }
    } else {
        missingPrivilege($USERNAME);
    }
} else if (array_key_exists("createContainer", $_POST) and array_key_exists("link", $_POST) and array_key_exists("port", $_POST)) { // Will create a container
    if ($PRIVILEGE["dockerAdmin"]) {
        $id = $USERNAME;
        $id .= rand();
        $id = sanitize(substr(sha1($id), 5));
        $port = intval($_POST["port"]);
        dbCommand("DELETE FROM docker WHERE ID='$id'");
        dbCommand("INSERT INTO docker VALUES (?, 'stopped', '', 'ubuntu', '$USERNAME', '$port', '$id')", [htmlspecialchars($OGPOST["link"])]);
        writeLog(28, "$USERNAME created container with id $id and with ip $address");
        echo "Created container with id $id";
        
    } else {
        missingPrivilege($USERNAME);
    }
}  else if (array_key_exists("deleteImage", $_POST)) { // Used to delete a container
    $name = $OGPOST["deleteImage"];
    $name2 = $_POST["deleteImage"];
    if ($PRIVILEGE["dockerAdmin"]) {
        if (dbRequest2("SELECT * FROM dockerImages WHERE realName=?", "*", [$name])) {
            dbCommand("DELETE FROM dockerImages WHERE realName=?", [$name]);
            writeLog(28, "$USERNAME deleted image with name $name2 and with ip $address");
            echo "Deleted image";
        } else {
            http_response_code(404);
            echo "Could not find image";
        }
    } else {
        missingPrivilege($USERNAME);
    }
} else if (array_key_exists("createImage", $_POST) and array_key_exists("name", $_POST)) { // Will create a container
    if ($PRIVILEGE["dockerAdmin"]) {
        $name = $_POST["name"];
        $realName2 = $_POST["createImage"];
        $realName = htmlspecialchars($OGPOST["createImage"]);
        dbCommand("DELETE FROM dockerImages WHERE realName=?", [$realName]);
        echo $realName;
        dbCommand("INSERT INTO dockerImages VALUES (?, '$name')", [$realName]);
        writeLog(28, "$USERNAME created image with name $realName2 and with ip $address");
        echo "Created image with name $realName2";
        
    } else {
        missingPrivilege($USERNAME);
    }
} else {
    http_response_code(400);
    echo "Invalid command";
}