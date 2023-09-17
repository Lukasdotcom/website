var script = document.createElement('script');
script.src = "<?php
echo "https" . '://' . $_SERVER["HTTP_HOST"]; ?>/cookieClicker/index.js";
<?php
header("Content-Type: text/javascript");
header("Cache-Control: public, max-age=864000");
header("Access-Control-Allow-Origin: *");
?>
script.id = "hostname";
document.head.appendChild(script);