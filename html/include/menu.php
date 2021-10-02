<?php
$MENUITEMS = [["Main Page", "/index.php"], ["Internet", "/internet.php"], ["Dice Game", "/diceGame/index.php"], ["Space 3", "/space3/index.php"], ["Electricity Log", "/electricity.php"], ["Floppy", "/floppy.php"], ["Soccer", "/soccer.php"], ["privilege", "viewLog", "/log/index.php", "Server Log"], ["Cookie Clicker Addon", "/cookieClicker/index.php"], ["user", "/usermenu/index.php", "User Menu"], ["notUser", "/login.php", "Login/Signup"], ["user", "/login.php", "Logout"]];
echo "<div class='vertical-menu'>";
function menuItem($link, $name)
{
    $location = $_SERVER["PHP_SELF"];
    if ($location == $link) {
        echo "<a href='$link' class='active'>$name</a>";
    } else {
        echo "<a href='$link'>$name</a>";
    }
}
foreach ($MENUITEMS as $menu) {
    $menuLink = $menu[1];
    $menuName = $menu[0];
    switch ($menuName) {
        case "notUser":
            if (!$USERNAME) {
                menuItem($menu[1], $menu[2]);
            }
            break;
        case "user":
            if ($USERNAME) {
                menuItem($menu[1], $menu[2]);
            }
            break;
        case "privilege":
            if ($USERNAME and $PRIVILEGE[$menu[1]]) {
                menuItem($menu[2], $menu[3]);
            }
            break;
        default:
            menuItem($menuLink, $menuName);
            break;
    }
}
echo "</div>";
