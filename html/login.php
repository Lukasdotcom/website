<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
    <title>
        Login
    </title>
    <?php
    $DESCRIPTION = "Login page for my personal website.";
    include 'include/all.php';
    if ($USERNAME) {
        setcookie("user", "", time() - 300);
        $USERNAME = Null;
    }
    $jsonInfo = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/config.json");
    $jsonInfo = json_decode($jsonInfo, true);
    $SITEKEY = $jsonInfo["turnstileSitekey"];
    echo "<script>const sitekey='$SITEKEY'</script>";
    ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=turnstileCb" async defer></script>
</head>

<body>
    <?php
    include 'include/menu.php';
    echo "<div class='main'>
        <script type='text/javascript' src='javascript/login.js' async defer ></script>";
    ?>
    <h1>Login or Signup Here</h1>
    <?php
    if ($USERNAME) {
        echo "You have logged out<br><br>";
    }
    // If logged out a login form will come
    echo "<label for='username'>Username:</label><br>
            <input type='text' id='username' name='username'/><br>
            <label for='password'>Password:</label><br>
            <input type='password' id='password' name='password'/><br>
            <input type='checkbox' id='signup' name='signup' value='True'>
            <label for='signup'>Check this to signup</label><br>
            <div id='challenge'></div>
            <button style='size:50px' id='login'>Login/Signup</button>
        <p style='color:red' id='status'></p>";
    ?>
    </div>
</body>

</html>