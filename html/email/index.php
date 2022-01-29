<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family
    </title>
    <?php
    $DESCRIPTION = "A way to easily send emails through sendgrid.";
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
    } else if (! $PRIVILEGE["docker"]) {
        http_response_code(403);
        header("Refresh:3; url=/index.php", true);
        echo "<h2>Forbidden redirecting...</h2>";
    } else {
        ?>
        <script type="text/javascript" src="index.js"></script>
        <h1>Send Emails</h1>
        <label for="sender">Sending Email: </label>
        <input name="sender" id="sender">
        <br>
        <label for="reciever">Recipient's Email: </label>
        <input name="reciever" id="reciever">
        <br>
        <label for="subject">Subject: </label>
        <input name="subject" id="subject">
        <br>
        <p style="color: red;">Warning do not paste unsafe input into the textbox below</p>
        <label for="body">Body: </label>
        <br>
        <textarea name="body" id="body" style="width: 100%;" rows="10"></textarea>
        <h3>HTML Render of Email</h3>
        <div style="border:1px solid white;border-radius: 4px;padding: 5px;" id="render"></div>
        <br>
        <button id="send">Send</button>
        <?php
    }
    ?>
    </div>
</body>

</html>