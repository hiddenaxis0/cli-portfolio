<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name   = strip_tags(trim($_POST["first_name"] ?? ""));
    $last_name    = strip_tags(trim($_POST["last_name"] ?? ""));
    $email        = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
    $phone        = strip_tags(trim($_POST["phone"] ?? ""));
    $inquiry_type = strip_tags(trim($_POST["inquiry_type"] ?? ""));
    $project      = strip_tags(trim($_POST["project"] ?? ""));
    $message      = strip_tags(trim($_POST["message"] ?? ""));

    $name = $first_name . " " . $last_name;

    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "error";
        exit;
    }

    $to      = "contact@sidneyfranklin.org";
    $subject = "New Inquiry from sidneyfranklin.com" . ($inquiry_type ? " — $inquiry_type" : "");

    $body  = "You have a new message from sidneyfranklin.com\n\n";
    $body .= "----------------------------\n";
    $body .= "Name:         $name\n";
    $body .= "Email:        $email\n";
    if ($phone)        $body .= "Phone:        $phone\n";
    if ($inquiry_type) $body .= "Inquiry Type: $inquiry_type\n";
    if ($project)      $body .= "Project:      $project\n";
    $body .= "----------------------------\n\n";
    $body .= "Message:\n$message\n";

    $headers  = "From: noreply@sidneyfranklin.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $body, $headers)) {
        echo "success";
    } else {
        echo "error";
    }

} else {
    echo "error";
}
?>
