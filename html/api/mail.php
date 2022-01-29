<?php
require_once "api.php";
if (! $PRIVILEGE["mail"]) { // Makes sure that the person has the right privilege
    missingPrivilege($USERNAME);
    exit();
}
use PHPMailer\PHPMailer\PHPMailer;
if (array_key_exists("mail", $OGPOST) and array_key_exists("sender", $OGPOST) and array_key_exists("subject", $OGPOST) and array_key_exists("body", $OGPOST)) {
    $mail = new PHPMailer(true);
    // Server Settings
    $mail->SMTPDebug = 0; // Prevents debugging
    $mail->isSMTP(); // Enables SMTP
    $mail->Host = 'smtp.sendgrid.net'; // Specify SMTP server
    $mail->SMTPAuth = true; // Enable SMTP authentication
    // Gets the neccessary password for the credentials
    $jsonInfo = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/config.json");
    $jsonData = json_decode($jsonInfo, true);
    $password = $jsonData["mail"];
    $mail->Username = 'apikey'; // SMTP username
    $mail->Password = $password; // SMTP password
    $mail->SMTPSecure = 'tls';//PHPMailer::ENCRYPTION_STARTTLS; Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
    $mail->Port = 587; // TCP port to connect to
    // Message content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = $OGPOST["subject"]; // The subject
    $mail->Body = $OGPOST["body"]; // The body of the email

    // Recipients
    if (array_key_exists("senderName", $OGPOST)) { // Who to send it from. This will check if a short name was given
        $mail->setFrom($OGPOST["sender"], $OGPOST["senderName"]);
    } else {
        $mail->setFrom($OGPOST["sender"]);
    }
    $mail->addAddress($OGPOST["mail"]); // Add a recipient
    $mail->send();
} else {
    http_response_code(400);
    echo "Invalid command";
}