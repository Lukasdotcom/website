<?php
// Will check if this is requested through cloudflare
if (array_key_exists("Cf-Connecting-Ip", apache_request_headers())) {
    $address = sanitize(apache_request_headers()["Cf-Connecting-Ip"]);
    if (!$address) {
        $address = $_SERVER["REMOTE_ADDR"]; // Variable that stores the IP address of user accessing the website
    }
} else {
    $address = $_SERVER["REMOTE_ADDR"];
}
