<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(400);
    exit("error");
}

$first_name   = trim(strip_tags($_POST["first_name"] ?? ""));
$last_name    = trim(strip_tags($_POST["last_name"] ?? ""));
$email        = trim($_POST["email"] ?? "");
$phone        = trim(strip_tags($_POST["phone"] ?? ""));
$inquiry_type = trim(strip_tags($_POST["inquiry_type"] ?? ""));
$project      = trim(strip_tags($_POST["project"] ?? ""));
$message      = trim(strip_tags($_POST["message"] ?? ""));

$name = trim("$first_name $last_name");

if (!$first_name || !$last_name || !$message || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("error");
}

$to = "contact@sidneyfranklin.org";
$subject = "New Inquiry" . ($inquiry_type ? " — $inquiry_type" : "");

$body =
"New message:\n\n".
"Name: $name\n".
"Email: $email\n".
($phone ? "Phone: $phone\n" : "") .
($inquiry_type ? "Inquiry: $inquiry_type\n" : "") .
($project ? "Project: $project\n" : "") .
"\nMessage:\n$message\n";

$headers  = "From: noreply@sidneyfranklin.org\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$mail_sent = mail($to, $subject, $body, $headers);

// LOG (good fallback)
$dir = __DIR__ . "/messages";
if (!is_dir($dir)) mkdir($dir, 0755, true);

file_put_contents(
    $dir . "/messages.jsonl",
    json_encode([
        "time" => date("c"),
        "email" => $email,
        "name" => $name,
        "message" => $message,
        "mail_sent" => $mail_sent
    ], JSON_UNESCAPED_UNICODE) . "\n",
    FILE_APPEND
);

echo $mail_sent ? "success" : "error";