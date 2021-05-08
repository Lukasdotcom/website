<!DOCTYPE html>
<html>

<head>
    <title>
        Schaefer Family - User Page
    </title>
    <?php
    include 'include/all.php';
    // Checks for a signup and creates that user if neccessary
    if ($_POST["signup"]) {
        $RESULT = dbRequest("username", "users", "username", $_POST["username"], 0);
        if ($RESULT == False) {
            $PASSWORD = password_hash($_POST["password"], PASSWORD_BCRYPT);
            dbAdd([$_POST["username"], $PASSWORD], "users");
        }
    }
    // Will check if a username and password was sent to the server and checks if that pair exists in the database
    if ($_POST["username"] != NULL and $_POST["password"] != NULL and $USERNAME == NULL) {
        $RESULT = dbRequest("password", "users", "username", $_POST["username"], 0);
        if (password_verify($_POST["password"], $RESULT[0])) {
            // If the username and password are valid a cookie entry is put into the database and the cookie is put on the user
            $USERNAME = $_POST["username"];
            $Time = time() + 3600;
            $Cookie = $USERNAME;
            $Cookie .= $Time;
            $Cookie = sanitize(substr(password_hash($_POST["password"], PASSWORD_BCRYPT),15));
            $Test = [$Cookie, $USERNAME, $Time];
            dbAdd($Test, "cookies");
            setcookie("user", $Cookie, time() + 600, "/");
            header("Refresh:0; url=/usermenu.php");
        }
    }
    if ($USERNAME == NULL) {
        header("Refresh:3; url=/login.php");
    }
    ?>
</head>

<body>
    <?php
    include 'include/menu.php';
    echo "<div class='main'>";
    if ($USERNAME == NULL) { // Checks that the user is logged in or if the new user or login information is valid
        if ($_POST["username"] == NULL) {
            echo "<h2>You are not logged in redirecting...</h2>";
        } else {
            if ($_POST["signup"] != True) {
                echo "<h2>Wrong password or username redirecting...</h2>";
            } else {
                echo "<h2>This username is already taken...</h2>";
            }
        }
    } else {
        
        echo "<h1>Edit User(s) Here</h1>";
        if ($_POST["create"]) {
            $USERNAME2 = $_POST["username"];
            $RESULT = dbRequest("username", "users", "username", $USERNAME2, 0);
            if ($RESULT == False) {
                $PASSWORD2 = $_POST["password"];
                $PASSWORD = password_hash($_POST["password"], PASSWORD_BCRYPT);
                dbAdd([$USERNAME2, $PASSWORD], "users");
                echo "You have created a new user with username $USERNAME2 and password <span title='$PASSWORD2'>(hold cursor over this text to see)</span><br><br>";
            } else {
                echo "Username; $USERNAME2 is already being used<br><br>";
            }
        }
        if ($_POST["delete"]) {
            if (!$PRIVILEGE["deleteUser"]) {
                $_POST['user'] = $USERNAME;
            }
            if (!root($_POST["user"])) {
                $user = $_POST['user'];
                echo "User $user has been deleted<br>";
                dbRemove("privileges", "username", $user, 0);
                dbRemove("users", "username", $user, 0);
                dbRemove("cookies", "username", $user, 0);
            } else {
                echo "User $user has root privileges<br>";
            }
        } elseif ($_POST["saveEdit"] == True and (!root($_POST["OGUsername"]) or $PRIVILEGE["root"])) {
            $oldUsername = $_POST['OGUsername'];
            if (!$PRIVILEGE["editUser"]) {
                $oldUsername = $USERNAME;
            }
            echo "Saved edits on user $oldUsername<br>";
            if ($_POST["password"]) {
                dbEdit("users", ["password", password_hash($_POST["password"], PASSWORD_BCRYPT)], ["username", $oldUsername], 0);
                $newPass = $_POST["password"];
                echo "new password of <span title='$newPass'>(hold cursor over this text to see)</span><br>";
            }
            dbRemove("privileges", "username", $oldUsername, 0);
            foreach ($PRIVILEGE as $type=>$ignore) {
                if ($_POST[$type]) {
                    dbAdd([$oldUsername, $type], "privileges");
                    echo "User has privilege: $type<br>";
                }
            }
            if ($_POST["username"] and !dbRequest("username", "users", "username", $_POST["username"], 0)) {
                dbEdit("users", [["username", $_POST["username"]]], ["username", $oldUsername], 0);
                dbEdit("privileges", [["username", $_POST["username"]]], ["username", $oldUsername], 0);
                $newName = $_POST['username'];
                echo "new username of $newName";
            }
            echo "<br>";
        }
        echo '<form method="post" action="/usermenu.php" autocomplete="off">';
        if ($_POST["edit"] != True) {
            if ($PRIVILEGE["editUser"] or $PRIVILEGE["deleteUser"]) {
                echo '<label for="user">Choose a user:</label>
                            <select name="user">';
                $LIST = dbRequest("username", "users", NULL, NULL, 2);
                foreach ($LIST as $USER) {
                    var_dump($PRIVILEGE);
                    if (!root($USER) or $PRIVILEGE["root"]) {
                        echo "<option value='$USER'><a>$USER</a></option>";
                    }
                }
                echo '</select><br>';
            }
            if ($PRIVILEGE["editUser"]) {
                echo '<button name="edit" value="True" type="submit<br>">Edit the User</button><br>';
            } else {
                echo '<button name="edit" value="True" type="submit<br>">Edit your user</button><br>';
            }
            if ($PRIVILEGE["deleteUser"]) {
                echo  '<button name="delete" value="True" type="submit<br>">Delete the User</button><br>';
            } else {
                echo '<button name="delete" value="True" type="submit<br>">Delete your user</button><br>';
            }
            $text = ["create", "Create new user", "password"];
        } else {
            if ($PRIVILEGE["editUser"]) {
                $user = $_POST['user'];
            } else {
                $user = $USERNAME;
            }
            $existing = dbRequest("privilege", "privileges", "username", $user, 0);
            if ($existing == False) {
                $existing = [];
            }
            echo "<h3>Privileges for $user </h3>";
            echo "<input type='hidden' name='OGUsername' value='$user'>";
            foreach ($PRIVILEGE as $type=>$ignore) {
                if ($PRIVILEGE[$type]) {
                    if (array_search($type, $existing) !== false) {
                        echo "<input type='checkbox' name='$type' checked='yes' value='True'>$type<br>";
                    } else {
                        echo "<input type='checkbox' name='$type' value='True'>$type<br>";
                    }
                }
            }
            $text = ["saveEdit", "Save", "text"];
        }
        echo "<br><label for='username'>Username:</label><br>
                <input type='text' id='username' name='username'/><br>
                <label for='password'>Password:</label><br>
                <input type='$text[2]' id='password' name='password'/><br>
                <button name='$text[0]' value='True' type='submit'>$text[1]</button><br>
            </form>";
    }
    ?>
    </div>
</body>

</html>