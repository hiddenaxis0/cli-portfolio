<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(400);
    echo "error";
    exit;
}

// Sanitize inputs
$first_name   = trim(strip_tags($_POST["first_name"] ?? ""));
$last_name    = trim(strip_tags($_POST["last_name"] ?? ""));
$email        = trim($_POST["email"] ?? "");
$phone        = trim(strip_tags($_POST["phone"] ?? ""));
$inquiry_type = trim(strip_tags($_POST["inquiry_type"] ?? ""));
$project      = trim(strip_tags($_POST["project"] ?? ""));
$message      = trim(strip_tags($_POST["message"] ?? ""));

$name = "$first_name $last_name";

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email) || empty($message)) {
    echo "error";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "error";
    exit;
}

// Email settings
$to = "contact@sidneyfranklin.org";
$subject = "New Inquiry from sidneyfranklin.com" . ($inquiry_type ? " — $inquiry_type" : "");

// Build message body
$body = "You have a new message from sidneyfranklin.com\n\n";
$body .= "Name:    $name\n";
$body .= "Email:   $email\n";
if ($phone)        $body .= "Phone:   $phone\n";
if ($inquiry_type) $body .= "Inquiry: $inquiry_type\n";
if ($project)      $body .= "Project: $project\n";
$body .= "\n" . str_repeat("-", 50) . "\n\n";
$body .= "$message\n";

// Headers - use a safe From address
$headers = "From: contact@sidneyfranklin.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email
$mail_sent = mail($to, $subject, $body, $headers);

// Save to file as backup
$storageDir = __DIR__ . DIRECTORY_SEPARATOR . 'messages';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

$logFile = $storageDir . DIRECTORY_SEPARATOR . 'messages.jsonl';
$logEntry = json_encode([
    'timestamp' => date('c'),
    'name'      => $name,
    'email'     => $email,
    'phone'     => $phone,
    'inquiry'   => $inquiry_type,
    'project'   => $project,
    'message'   => $message,
    'mail_sent' => $mail_sent
], JSON_UNESCAPED_UNICODE) . "\n";

@file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

// Return result
echo ($mail_sent) ? "success" : "error";
exit;
?>