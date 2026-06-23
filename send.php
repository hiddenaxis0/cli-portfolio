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

    // Use SMTP if configured. Otherwise fall back to PHP mail() only if sendmail is present.
    $mail_sent = false;
    $mail_method = 'none';
    $smtp_host = getenv('SMTP_HOST');
    $mail_to = getenv('MAIL_TO') ?: $to;
    $mail_from = getenv('MAIL_FROM') ?: 'noreply@sidneyfranklin.com';
    $mail_from_name = getenv('MAIL_FROM_NAME') ?: 'Sidney Franklin';
    $sendmail_path = trim(ini_get('sendmail_path'));
    $sendmail_binary = preg_split('/\s+/', $sendmail_path)[0] ?? '';
    $sendmail_available = $sendmail_binary !== '' && is_executable($sendmail_binary);

    if ($smtp_host && file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER') ?: '';
            $mail->Password   = getenv('SMTP_PASS') ?: '';
            $smtp_secure      = strtolower(getenv('SMTP_ENCRYPTION') ?: 'tls');
            if ($smtp_secure === 'ssl') {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port       = getenv('SMTP_PORT') ? (int) getenv('SMTP_PORT') : 587;
            $mail->setFrom($mail_from, $mail_from_name);
            $mail->addAddress($mail_to);
            $mail->addReplyTo($email, $name);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);
            $mail_sent    = $mail->send();
            $mail_method  = 'smtp';
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('SMTP mail failed: ' . $e->getMessage());
            $mail_sent = false;
            $mail_method = 'smtp_error';
        }
    } elseif (function_exists('mail') && $sendmail_available) {
        $mail_sent = @mail($to, $subject, $body, $headers);
        $mail_method = 'sendmail';
        if (!$mail_sent) {
            error_log('PHP mail() failed using sendmail path: ' . $sendmail_path);
        }
    } else {
        $mail_method = 'none';
        error_log('No mail transport available. SMTP_HOST=' . ($smtp_host ?: 'none') . ', sendmail_path=' . $sendmail_path);
    }

    $payload = [
        'timestamp' => date('c'),
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
        'ua'        => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'mail_method' => $mail_method,
        'name'      => $name,
        'email'     => $email,
        'phone'     => $phone,
        'inquiry'   => $inquiry_type,
        'project'   => $project,
        'message'   => $message,
    ];

    $saved = false;
    $storageDir = __DIR__ . DIRECTORY_SEPARATOR . 'messages';
    if (!is_dir($storageDir)) {
        @mkdir($storageDir, 0755, true);
    }
    $logFile = $storageDir . DIRECTORY_SEPARATOR . 'messages.jsonl';
    $line = json_encode($payload, JSON_UNESCAPED_UNICODE) . "\n";
    $saved = @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX) !== false;

    // If either mail was sent or the message was saved to disk, report success.
    if ($mail_sent || $saved) {
        echo "success";
    } else {
        echo "error";
    }

} else {
    echo "error";
}
?>
