<!DOCTYPE html>
<html>

<head>
    <title>
        Schaefer Family - Maintenance
    </title>
    <?php
    header('Retry-After: 3600');
    http_response_code(503);
    ?>
</head>
<style>
    body {
        background-color: black;
    }
</style>

<body>
    <h1 style='font-size: 50px; color: red; font-weight: 850;'>The website is currently under maintenance or has crashed please wait!</h1>
    <p style="color: white;">Note for system administrator. The error message is in error.log of the website root directory.</p>
</body>