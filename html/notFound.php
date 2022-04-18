<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
    <title>
        Page Not Found
    </title>
    <?php
    $DESCRIPTION = "This page could not be found.";
    require_once 'include/all.php';
    ?>
</head>

<body>
    <?php
    include 'include/menu.php';
    echo "<div class='main'>";
    ?>
    <h1>404 Not Found</h1>
    <h3>Floppy could sadly not find your page :(</h3>
    <p>She did find some food on the way though.</p>
    <?php
    createImage("notFound", "Picture of the great bun bun Floppy finding food.");
    ?>
    </div>
</body>

</html>