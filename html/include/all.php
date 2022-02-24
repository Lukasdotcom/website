<?php
include 'functions.php';
// Contains the favicon, the css stylesheet, meta tags, and js
echo '<meta charset="utf=8" />
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
    <script type="text/javascript" src="/javascript/cookie.js"></script>';
if (false) {
  echo '<script type="text/javascript">
      (function(c,l,a,r,i,t,y){
          c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
          t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
          y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
      })(window, document, "clarity", "script", "6nguibuvtp");
    </script>';
}

if (isset($DESCRIPTION)) {
  echo "<meta name='Description' content='$DESCRIPTION'>";
} else {
  echo "<meta name='Description' content='No Description Available'>";
}
if (false) {
  // Adds google analytics
  echo "<!-- Global site tag (gtag.js) - Google Analytics --> <script async src='https://www.googletagmanager.com/gtag/js?id=G-LDTH4Z14QQ'></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-LDTH4Z14QQ'); </script>";
}
/**
 * Creates an image that loads a simple image before loading the entire image.
 * @param string $img used as the name of the image
 */
function createImage($img, $alt, $style="width:100%;") {
  echo "<img id='first$img' onload='imageLoad(`$img`, `first`)' src='/img/$img.first.jpg' alt='$alt' style='$style'>";
  echo "<img id='min$img' src='' alt='$alt' style='$style display: none;'>";
  echo "<img id='$img' src='' alt='$alt' style='$style display: none;'>";
}