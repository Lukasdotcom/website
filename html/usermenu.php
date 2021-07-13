<!DOCTYPE html>
<html>

<head>
    <title>
        Schaefer Family - User Page
    </title>
    <?php
    $DESCRIPTION = "Menu for editing users and deleting them.";
    include 'include/all.php';
    ?>
</head>

<body>
    <script type='text/javascript' src='javascript/usermenu.js'></script>
    <?php
    include 'include/menu.php';
    echo "<div class='main'>";
    if ($USERNAME == NULL) { // Checks that the user is logged in or if the new user or login information is valid
        header("Refresh:3; url=/login.php");
        echo "<h2>You are not logged in redirecting...</h2>";
    } else {
        echo "<h1>Edit User(s) Here</h1>";
        $firstUser = $USERNAME;
        if ($PRIVILEGE["editUser"] or $PRIVILEGE["deleteUser"]) {
            echo '<label for="user">Choose a user:</label>
                        <select id="user" name="user" onchange="console.log(document.getElementById(`user`).value)">';
            $LIST = dbRequest("username", "users", NULL, NULL, 2);
            foreach ($LIST as $USER) {
                if (!root($USER) or $PRIVILEGE["root"]) {
                    echo "<option value='$USER'><a>$USER</a></option>";
                }
            }
            echo '</select><br>';
            // Will make the selection to your user
            echo "<script>editUserStart('$USERNAME')</script>";
        }
        echo "<h3>Privileges for $USERNAME</h3>";
        foreach ($PRIVILEGE as $type => $ignore) {
            echo "<input type='checkbox' name='$type' id='$type' value='True' checked>$type<br>";
        }
    }
    ?>
    </div>
</body>

</html>