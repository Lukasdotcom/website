<?php
include 'functions.php';
// Contains the favicon, the css stylesheet, meta tags, and clarity
echo '<meta http-equiv="content-language" content="en-us">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="/css/website.css" />';
if (false) {
  echo '<script type="text/javascript">
      (function(c,l,a,r,i,t,y){
          c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
          t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
          y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
      })(window, document, "clarity", "script", "6nguibuvtp");
    </script>';
}

try {
  echo "<meta name='Description' content='$DESCRIPTION'>";
} catch (Exception $e) {
  echo "<meta name='Description' content='No Description Available'>";
}
if (false) {
  // Adds google analytics
  echo "<!-- Global site tag (gtag.js) - Google Analytics --> <script async src='https://www.googletagmanager.com/gtag/js?id=G-LDTH4Z14QQ'></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-LDTH4Z14QQ'); </script>";
}