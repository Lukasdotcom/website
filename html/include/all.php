<?php
include 'functions.php';
// Contains the favicon, the css stylesheet, meta tags, and js
?>
<meta charset="utf=8" />
<meta property="og:site_name" content="lschaefer" />
<meta http-equiv="content-language" content="en-us">
<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
<link rel="manifest" href="/favicon/site.webmanifest">
<link rel="stylesheet" type="text/css" href="/css/website.css" />
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/jquery-ui.min.js"></script>
<link rel="stylesheet" href="/css/jquery-ui.min.css">
<script type="text/javascript" src="/javascript/functions.js"></script>
<script type="text/javascript" src="/javascript/cookie.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<?php
if ($MOBILE) { // Tells javascript if this is a mobile user
  echo "<script type='text/javascript'>var mobile = true</script>";
} else {
  echo "<script type='text/javascript'>var mobile = false</script>";
}
if (isset($DESCRIPTION)) {
  echo "<meta name='Description' content='$DESCRIPTION'>";
} else {
  echo "<meta name='Description' content='No Description Available'>";
}
/**
 * Creates an image that loads a simple image before loading the entire image.
 * @param string $img used as the name of the image
 */
function createImage($img, $alt, $style="width:100%;") {
  global $MOBILE;
  echo "<img id='first$img' onload='imageLoad(`$img`, `first`)' src='/img/$img.first.jpg' alt='$alt' style='$style'>";
  echo "<img id='min$img' src='' alt='$alt' style='$style display: none;'>";
  if (! $MOBILE) {
    echo "<img id='$img' src='' alt='$alt' style='$style display: none;'>";
  }
}