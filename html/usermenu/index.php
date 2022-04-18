<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <title>
        User Page
    </title>
    <?php
    $DESCRIPTION = "Menu for editing users and deleting them.";
    include '../include/all.php';
    ?>
</head>

<body>
    <?php
    include '../include/menu.php';
    echo "<div class='main'>";
    if (! $USERNAME) { // Checks that the user is logged in or if the new user or login information is valid
        header("Refresh:3; url=/login.php", true);
        echo "<h2>You are not logged in redirecting...</h2>";
    } else {
        echo "<script type='text/javascript' src='index.js'></script>
        <script>var username='$USERNAME'</script>";
        echo "<h1>Edit User(s) Here</h1>";
        echo '<label for="user">Choose a user:</label>
                        <select id="user" name="user">';
        if (! $PRIVILEGE["editUser"]) {
            echo "<script>var editUser = false</script>";
        } else {
            echo "<script>var editUser = true</script>";
        }
        if ($PRIVILEGE["editUser"] or $PRIVILEGE["deleteUser"]) {
            $LIST = dbRequest("username", "users", NULL, NULL, 2);
            foreach ($LIST as $USER) {
                if (!root($USER) or $PRIVILEGE["root"]) {
                    echo "<option value='$USER'><a>$USER</a></option>";
                }
            }
        } else {
            echo "<option value='$USERNAME'><a>$USERNAME</a></option>";
        }
        echo '</select><br>';
        echo "<br><button id='delete' type='button'>Delete User</button>";
        echo "<h3 id='header'>Privileges for $USERNAME</h3>";
        echo "<div id='privilege'></div>";
        echo "<button id='save' type='button'>Save</button><br><br>";
        echo "<div id='passwordChange'>";
        echo "<label for='password'>New Password: </label>";
        echo "<input type='password' id='password' name='password'><br>";
        echo "<button id='changePassword' type='button'>Change Password</button>";
        echo "</div>";
        echo "<br><button id='upload' type='button'>Save Preferences to Cloud</button>";
        echo "<br><button id='download' type='button'>Download Preferences from Cloud</button>";
    }
    ?>
    <p id='saveStatus' style='color: green'> </p>
    </div>
</body>

</html>