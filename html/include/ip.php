<?php
// Will check if this is requested through cloudflare
$address = sanitize(apache_request_headers()["CF-Connecting-IP"]);
if (! $address) {
    $address = $_SERVER["REMOTE_ADDR"]; // Variable that stores the IP address of user accessing the website
}