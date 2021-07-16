<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family
    </title>
    <?php
    $DESCRIPTION = "Cookie clicker mod located here";
    require_once '../include/all.php';
    ?>
</head>

<body>
    <?php
    require_once '../include/menu.php';
    echo "<div class='main'>";
    ?>
    <p>javascript:(function() {
    var script = document.createElement('script');
    script.src = "http://localhost/cookieClicker/index.js";
    document.head.appendChild(script);
    }());</p>
    </div>
</body>

</html>