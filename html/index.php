<!DOCTYPE html>

<html dir="ltr" lang="en">
<?php
if (!file_exists("config.json")) {
    echo '<link rel="stylesheet" type="text/css" href="/css/website.css?v=1.0.3" />';
    echo "<h2>Missing configuration please input configuration</h2>";
}
?>

<head>
    <meta name="msvalidate.01" content="52F3C351645EB7619858FA8FCB20C441" />
    <title>
        lschaefer
    </title>
    <?php
    $DESCRIPTION = "Main page of my personal website.";
    include 'include/all.php';
    ?>
</head>

<body>
    <?php
    include 'include/menu.php';
    ?>
    <div class='main'>
        <h1>Welcome to my personal website!</h1>
        <p>This website is a personal project by Lukas Schaefer to practice web development and the source code for this website is on <a href="https://github.com/Lukasdotcom/website" rel="noopener noreferrer" target="_blank">github</a>.
            If you find any bugs or want to request a feature feel free to open a github issue and I will look at it. If you find a security vulnerability or issue in my code please contact me directly at
            <a href="mailto:security@lschaefer.xyz?subject=Website%20Vulnerability&amp;body=Hi%2C%20your%20website%20has%20a%20vulnerability.%20The%20vulnerability%20is%20...%20If%20you%20want%20to%20test%20it%20out%20these%20are%20the%20steps%20to%20reproduce%20it%20...%0AFrom%2C%20NAME">security@lschaefer.xyz</a>
        </p>
        <a href="/floppy.php">
            <?php
            createImage("floppy", "Picture of the great bun bun Floppy.");
            ?>
        </a>
    </div>
</body>

</html>