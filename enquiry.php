<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/PHPMailer-master/src/Exception.php';

/* ============================
   CONFIG
============================ */
define('RECAPTCHA_SECRET', '6Lf4cWEtAAAAAGBLPCc8Cg30QImZliXI7yq0mR-_');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'surefix');

define('SMTP_HOST', 'mbihosting.in');
define('SMTP_USER', 'noreply@mbihosting.in');
define('SMTP_PASS', '@#+FGA+1Un7]M,0u');
define('ADMIN_EMAIL', 'smrita@matrixbricks.com');
define('DEBUG_MODE', true);

/* ============================
   BLOCK DIRECT BROWSER VISITS
   (must run before any output)
============================ */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: /index.html');
    exit;
}

header('Content-Type: application/json');

/* ============================
   COLLECT DATA
============================ */
$name      = trim($_POST['name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$location  = trim($_POST['location'] ?? '');
$message   = trim($_POST['message'] ?? '');
$recaptcha = trim($_POST['g-recaptcha-response'] ?? '');

$errors = [];

/* ============================
   FIELD VALIDATION
============================ */
if ($name === '' || !preg_match("/^[A-Za-z\s.'-]{2,50}$/", $name)) {
    $errors['name'] = "Enter a valid name (letters only).";
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Enter a valid email address.";
}
if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
    $errors['phone'] = "Phone must be 10 to 15 digits.";
}
if ($location === '' || !preg_match("/^[A-Za-z\s.,'-]{2,50}$/", $location)) {
    $errors['location'] = "Enter a valid city name.";
}
if ($message === '' || strlen($message) < 10) {
    $errors['message'] = "Message must be at least 10 characters.";
}

/* ============================
   reCAPTCHA VERIFICATION
============================ */
if ($recaptcha === '') {
    $errors['recaptcha'] = "Please verify that you are not a robot.";
} else {
    $verifyData = http_build_query([
        'secret'   => RECAPTCHA_SECRET,
        'response' => $recaptcha,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ]);

    $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $verifyData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $verifyResponse = curl_exec($ch);
    curl_close($ch);

    $captchaResult = json_decode($verifyResponse, true);

    if (empty($captchaResult['success'])) {
        $errors['recaptcha'] = "Captcha verification failed. Please try again.";
    }
}

if (!empty($errors)) {
    echo json_encode(["status" => "error", "errors" => $errors]);
    exit;
}

/* ============================
   DATABASE INSERT
============================ */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Connection Failed"]);
    exit;
}
$conn->set_charset("utf8mb4");

$stmt = $conn->prepare("INSERT INTO enquiries (name, email, phone, location, message, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$stmt->bind_param("ssssss", $name, $email, $phone, $location, $message, $ip);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Database insert failed."]);
    exit;
}
$stmt->close();
$conn->close();

/* ============================
   EMAIL TEMPLATE
============================ */
function emailTemplate($title, $content) {
    return "
    <div style='font-family:Arial,Helvetica,sans-serif;max-width:600px;margin:auto;border:1px solid #ddd'>
        <div style='background:#000;padding:18px;text-align:center'>
            <img src='https://surefix.co.in/images/home/logo.webp' alt='Surefix' style='max-width:180px;'>
        </div>
        <div style='padding:22px;color:#333;font-size:14px;line-height:1.6'>
            <h2 style='margin-top:0;color:#111'>{$title}</h2>
            {$content}
        </div>
        <div style='background:#f2f2f2;padding:12px;text-align:center;font-size:12px;color:#666'>
            © " . date('Y') . " Surefix — S Doshi Papers Industries Private Limited
        </div>
    </div>";
}

$e = function ($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); };

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->isHTML(true);
    $mail->CharSet    = "UTF-8";

    /* Uncomment if your host uses a self-signed mail certificate */
    /*
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];
    */

    /* ---- ADMIN MAIL ---- */
    $mail->setFrom(SMTP_USER, 'Surefix Website Enquiry');
    $mail->addAddress(ADMIN_EMAIL);
    $mail->addReplyTo($email, $name);
    $mail->Subject = "New Enquiry from Surefix Website";

    $adminContent = "
        <p><b>Name:</b> " . $e($name) . "</p>
        <p><b>Email:</b> " . $e($email) . "</p>
        <p><b>Phone:</b> " . $e($phone) . "</p>
        <p><b>Location:</b> " . $e($location) . "</p>
        <hr>
        <p><b>Message:</b><br>" . nl2br($e($message)) . "</p>
        <p style='font-size:12px;color:#888'>Submitted on " . date('d M Y, h:i A') . "</p>
    ";

    $mail->Body = emailTemplate("New Enquiry Received", $adminContent);
    $mail->send();

    /* ---- USER ACKNOWLEDGEMENT ---- */
    $mail->clearAddresses();
    $mail->clearReplyTos();
    $mail->addAddress($email, $name);
    $mail->setFrom(SMTP_USER, 'Surefix');
    $mail->addReplyTo('smrita@matrixbricks.com', 'Surefix Sales');
    $mail->Subject = "Thank You for Contacting Surefix";

    $userContent = "
        <p>Hi " . $e($name) . ",</p>
        <p>Thank you for reaching out to <b>Surefix</b>. We have received your enquiry and our team will review it shortly.</p>
        <p>One of our representatives will get back to you within <b>1–2 working days</b>.</p>
        <br>
        <p>Regards,<br><b>Team Surefix</b><br>
        <a href='mailto:sales@surefix.co.in'>sales@surefix.co.in</a> | +91 95797 26091</p>
    ";

    $mail->Body = emailTemplate("Thank You!", $userContent);
    $mail->send();

    echo json_encode(["status" => "success", "message" => "Thank you! Your enquiry has been submitted."]);

} catch (Exception $ex) {
    /* Data is saved; mail failed. Keep user-facing success. */
    /* While testing, swap the line below for:
       echo json_encode(["status" => "error", "message" => $mail->ErrorInfo]); */
    echo json_encode(["status" => "success", "message" => "Your enquiry has been recorded. We will contact you shortly."]);
}