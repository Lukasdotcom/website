<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family - Login
    </title>
    <?php
    $DESCRIPTION = "Login page for Schaefer family.";
    include 'include/all.php';
    if ($USERNAME) {
        setcookie("user", "", time() - 300);
        $USERNAME = Null;
    }
    ?>
</head>

<body>
    <?php
    include 'include/menu.php';
    echo "<div class='main'>
        <script type='text/javascript' src='javascript/login.js'></script>";
    ?>
    <h1>Login or Signup Here</h1>
    <?php
    if ($USERNAME) {
        echo "You have logged out<br><br>";
    }
    // If logged out a login form will come
    echo '<label for="username">Username:</label><br>
            <input type="text" id="username" name="username"/><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"/><br>
            <input type="checkbox" id="signup" name="signup" value="True">
            <label for="signup">Check this to signup</label><br>
            <button onClick="login(document.getElementById(`username`).value, document.getElementById(`password`).value, document.getElementById(`signup`).checked)">Login/Signup</button>
        <p style="color:red" id="status"></p>';
    ?>
    </div>
</body>

</html>