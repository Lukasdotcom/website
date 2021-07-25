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
    <?php
    include 'include/menu.php';
    echo "<div class='main'>";
    if ($USERNAME == NULL) { // Checks that the user is logged in or if the new user or login information is valid
        header("Refresh:3; url=/login.php");
        echo "<h2>You are not logged in redirecting...</h2>";
    } else {
        echo "<script type='text/javascript' src='javascript/usermenu.js'></script>
        <script type='text/javascript' src='javascript/functions.js'></script>";
        echo "<h1>Edit User(s) Here</h1>";
        echo '<label for="user">Choose a user:</label>
                        <select id="user" name="user">';
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
        echo "<h3 id='header'>Privileges for $USERNAME</h3>";
        echo "<div id='privilege'></div>";
        echo "<button id='save' type='button'>Save</button>";
    }
    ?>
    <p id='saveStatus' style='color: green'> </p>
    </div>
</body>

</html>