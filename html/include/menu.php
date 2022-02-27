<?php
$MENUITEMS = [["Main Page", "/index.php"], ["Internet", "/internet/index.php"], ["Dice Game", "/diceGame/index.php"], ["Space 3", "/space3/index.php"], ["Idle Bouncer", "/idleBouncer/index.php"], ["Golf", "/golf/index.php"], ["Cookie Clicker Addon", "/cookieClicker/index.php"], ["Floppy", "/floppy.php"], ["Soccer", "/soccer.php"], ["privilege", "docker", "/docker/index.php", "Docker Containers"], ["privilege", "dockerAdmin", "/docker/admin.php", "Docker Admin"], ["Electricity Log", "/electricity.php"], ["privilege", "viewLog", "/log/index.php", "Server Log"], ["privilege", "viewBackup", "/backup/index.php", "Backups"], ["privilege", "mail", "/email/index.php", "Email"], ["user", "/usermenu/index.php", "User Menu"], ["user", "/usermenu/key.php", "Session Manager"], ["notUser", "/login.php", "Login/Signup"], ["user", "/login.php", "Logout"], ["Uptime", "https://uptime.lschaefer.xyz/status"]];
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
echo '<div style="height: 50px;display: inline-block;"></div>'; // Used to make sure the scrollbar scrolls correctly
echo "</div>";
