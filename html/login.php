<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family
    </title>
    <?php
    include 'include/all.php';
    ?>
</head>

<body>
    <?php
    include 'include/menu.php';
    echo "<div class='main'>";
    ?>
    <h1>Login or Signup Here</h1>
    <?php
    if ($USERNAME != NULL) {
        setcookie("user", "", time() - 300);
        $USERNAME = Null;
        echo "You have logged out<br><br>";
    }
    // If logged out a login form will come
    echo '<form method="post" action="/usermenu.php">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username"/><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"/><br>
            <input type="checkbox" id="signup" name="signup" value="True">
            <label for="signup">Check this to signup</label><br>
            <input type="submit" value="Login">
        </form>';
    ?>
    </div>
</body>

</html>