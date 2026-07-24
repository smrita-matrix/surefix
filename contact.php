<?php
/* ============================================================
   BOOTSTRAP — must run before anything that can fail
============================================================ */
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

define('DBG_FILE', __DIR__ . '/contact_debug.log');

function dbg($msg) {
    @file_put_contents(DBG_FILE, '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL, FILE_APPEND);
}

dbg('========== BOOT ==========');
dbg('PHP version: ' . PHP_VERSION);
dbg('Script: ' . __FILE__);
dbg('METHOD: ' . ($_SERVER['REQUEST_METHOD'] ?? 'n/a'));
dbg('IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'n/a'));

/* Catch fatals and still return JSON instead of a 500 page */
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        dbg('FATAL: ' . $err['message'] . ' @ ' . $err['file'] . ':' . $err['line']);
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo json_encode([
            "status"  => "error",
            "message" => "FATAL: " . $err['message'] . ' @ line ' . $err['line']
        ]);
    }
});

set_error_handler(function ($no, $str, $file, $line) {
    dbg('PHP WARNING/NOTICE [' . $no . ']: ' . $str . ' @ ' . $file . ':' . $line);
    return false;
});

dbg('Error handlers registered.');

/* ============================================================
   LOAD PHPMAILER
============================================================ */
dbg('--- REQUIRE PHPMAILER START ---');

$pmBase = __DIR__ . '/PHPMailer-master/src/';
foreach (['PHPMailer.php', 'SMTP.php', 'Exception.php'] as $f) {
    if (!file_exists($pmBase . $f)) {
        dbg('MISSING FILE: ' . $pmBase . $f);
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "PHPMailer file missing: " . $f]);
        exit;
    }
    require_once $pmBase . $f;
    dbg('LOADED: ' . $f);
}

dbg('--- REQUIRE PHPMAILER END ---');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

/* ============================================================
   CONFIG
============================================================ */
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

dbg('CONFIG loaded. DB=' . DB_NAME . ' SMTP=' . SMTP_HOST);

/* ============================================================
   METHOD GUARD
============================================================ */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    dbg('ABORT: non-POST, redirecting');
    header('Location: /contact-us.html');
    exit;
}

header('Content-Type: application/json');
dbg('POST confirmed. JSON header sent.');
dbg('RAW POST KEYS: ' . implode(', ', array_keys($_POST)));

/* ============================================================
   COLLECT DATA
============================================================ */
$full_name    = trim($_POST['full_name'] ?? '');
$company_name = trim($_POST['company_name'] ?? '');
$city         = trim($_POST['city'] ?? '');
$mobile       = trim($_POST['mobile'] ?? '');
$email        = trim($_POST['email'] ?? '');
$recaptcha    = trim($_POST['g-recaptcha-response'] ?? '');

dbg('FIELD full_name=' . $full_name);
dbg('FIELD company_name=' . $company_name);
dbg('FIELD city=' . $city);
dbg('FIELD mobile=' . $mobile);
dbg('FIELD email=' . $email);
dbg('FIELD recaptcha length=' . strlen($recaptcha));

$errors = [];

/* ============================================================
   VALIDATION
============================================================ */
dbg('--- VALIDATION START ---');

if ($full_name === '' || !preg_match("/^[A-Za-z\s.'-]{2,50}$/", $full_name)) {
    $errors['full_name'] = "Enter a valid name (letters only).";
    dbg('FAIL: full_name');
} else { dbg('OK: full_name'); }

if ($company_name !== '' && !preg_match("/^[A-Za-z0-9\s.,&'-]{2,100}$/", $company_name)) {
    $errors['company_name'] = "Enter a valid company name.";
    dbg('FAIL: company_name');
} else { dbg('OK: company_name'); }

if ($city === '' || !preg_match("/^[A-Za-z\s.,'-]{2,50}$/", $city)) {
    $errors['city'] = "Enter a valid city name (letters only).";
    dbg('FAIL: city');
} else { dbg('OK: city'); }

if (!preg_match("/^[0-9]{10,15}$/", $mobile)) {
    $errors['mobile'] = "Mobile number must be 10 to 15 digits.";
    dbg('FAIL: mobile');
} else { dbg('OK: mobile'); }

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Enter a valid email address.";
    dbg('FAIL: email');
} else { dbg('OK: email'); }

dbg('--- VALIDATION END. errors=' . count($errors) . ' ---');

/* ============================================================
   RECAPTCHA
============================================================ */
dbg('--- RECAPTCHA START ---');

if ($recaptcha === '') {
    $errors['recaptcha'] = "Please verify that you are not a robot.";
    dbg('RECAPTCHA: empty token');
} else {
    $verifyData = http_build_query([
        'secret'   => RECAPTCHA_SECRET,
        'response' => $recaptcha,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
    dbg('RECAPTCHA: query built');

    $verifyResponse = false;

    if (function_exists('curl_init')) {
        dbg('RECAPTCHA: cURL available');
        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $verifyData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $verifyResponse = curl_exec($ch);
        dbg('RECAPTCHA: curl http_code=' . curl_getinfo($ch, CURLINFO_HTTP_CODE));
        if ($verifyResponse === false) dbg('RECAPTCHA cURL ERROR: ' . curl_error($ch));
        curl_close($ch);
    } else {
        dbg('RECAPTCHA: cURL MISSING, falling back');
        $verifyResponse = @file_get_contents('https://www.google.com/recaptcha/api/siteverify?' . $verifyData);
    }

    dbg('RECAPTCHA RAW: ' . var_export($verifyResponse, true));

    $captchaResult = json_decode($verifyResponse, true);
    dbg('RECAPTCHA DECODED: ' . json_encode($captchaResult));

    if (empty($captchaResult['success'])) {
        $errors['recaptcha'] = "Captcha verification failed. Please try again.";
        dbg('RECAPTCHA FAIL codes: ' . json_encode($captchaResult['error-codes'] ?? []));
    } else {
        dbg('RECAPTCHA PASS');
    }
}

dbg('--- RECAPTCHA END ---');

if (!empty($errors)) {
    dbg('EXIT with validation errors: ' . json_encode($errors));
    echo json_encode(["status" => "error", "errors" => $errors]);
    exit;
}

/* ============================================================
   DATABASE
============================================================ */
dbg('--- DB START ---');

if (!class_exists('mysqli')) {
    dbg('FATAL: mysqli extension not available');
    echo json_encode(["status" => "error", "message" => "mysqli extension missing on server."]);
    exit;
}
dbg('DB: mysqli class OK');

mysqli_report(MYSQLI_REPORT_OFF);

$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    dbg('DB CONNECT FAIL: ' . $conn->connect_error);
    echo json_encode([
        "status"  => "error",
        "message" => DEBUG_MODE ? ("DB connect failed: " . $conn->connect_error) : "Database error."
    ]);
    exit;
}
dbg('DB CONNECTED to ' . DB_NAME);

$conn->set_charset("utf8mb4");
dbg('DB charset set to utf8mb4');

/* Confirm the table exists before preparing */
$tblCheck = $conn->query("SHOW TABLES LIKE 'contact_enquiries'");
if (!$tblCheck || $tblCheck->num_rows === 0) {
    dbg('DB FAIL: table contact_enquiries does NOT exist');
    echo json_encode([
        "status"  => "error",
        "message" => DEBUG_MODE ? "Table 'contact_enquiries' does not exist. Run the CREATE TABLE query." : "Database error."
    ]);
    exit;
}
dbg('DB: table contact_enquiries exists');

$sql = "INSERT INTO contact_enquiries (full_name, company_name, city, mobile, email, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
dbg('DB SQL: ' . $sql);

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    dbg('DB PREPARE FAIL: ' . $conn->error);
    echo json_encode([
        "status"  => "error",
        "message" => DEBUG_MODE ? ("Prepare failed: " . $conn->error) : "Database error."
    ]);
    exit;
}
dbg('DB PREPARE OK');

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$stmt->bind_param("ssssss", $full_name, $company_name, $city, $mobile, $email, $ip);
dbg('DB BIND OK');

if (!$stmt->execute()) {
    dbg('DB EXECUTE FAIL: ' . $stmt->error);
    echo json_encode([
        "status"  => "error",
        "message" => DEBUG_MODE ? ("Insert failed: " . $stmt->error) : "Database error."
    ]);
    exit;
}
dbg('DB INSERT OK. insert_id=' . $stmt->insert_id);

$stmt->close();
$conn->close();
dbg('--- DB END ---');

/* ============================================================
   EMAIL TEMPLATE
============================================================ */
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

$esc = function ($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); };
dbg('Email template + escaper ready.');

dbg('--- MAIL START ---');

$mailSent  = false;
$mailError = '';
$mail      = null;

try {
    $mail = new PHPMailer(true);
    dbg('MAIL: PHPMailer instantiated');

    $mail->SMTPDebug   = 2;
    $mail->Debugoutput = function ($str, $level) { dbg('SMTP[' . $level . '] ' . trim($str)); };

    $mail->isSMTP();
    $mail->Host        = SMTP_HOST;
    $mail->SMTPAuth    = true;
    $mail->Username    = SMTP_USER;
    $mail->Password    = SMTP_PASS;
    $mail->SMTPSecure  = '';
    $mail->SMTPAutoTLS = false;
    $mail->Port        = 25;
    $mail->Timeout     = 20;
    $mail->isHTML(true);
    $mail->CharSet     = "UTF-8";
    $mail->SMTPOptions = [
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]
    ];
    dbg('MAIL: SMTP configured host=' . SMTP_HOST . ' port=25');

    /* ---- ADMIN ---- */
    $mail->setFrom(SMTP_USER, 'Surefix Contact Form');
    $mail->addAddress(ADMIN_EMAIL);
    $mail->addReplyTo($email, $full_name);
    $mail->Subject = "New Contact Form Submission - Surefix Website";

    $adminContent = "
        <p><b>Full Name:</b> " . $esc($full_name) . "</p>
        <p><b>Company Name:</b> " . ($company_name !== '' ? $esc($company_name) : '<i>Not provided</i>') . "</p>
        <p><b>City:</b> " . $esc($city) . "</p>
        <p><b>Mobile Number:</b> " . $esc($mobile) . "</p>
        <p><b>Email Address:</b> " . $esc($email) . "</p>
        <hr>
        <p style='font-size:12px;color:#888'>Submitted on " . date('d M Y, h:i A') . " | IP: " . $esc($ip) . "</p>
    ";
    $mail->Body = emailTemplate("New Contact Enquiry Received", $adminContent);
    dbg('MAIL: admin body built');

    $mail->send();
    dbg('MAIL: ADMIN SENT OK');

    /* ---- USER ---- */
    $mail->clearAddresses();
    $mail->clearReplyTos();
    $mail->addAddress($email, $full_name);
    $mail->setFrom(SMTP_USER, 'Surefix');
    $mail->addReplyTo('sales@surefix.co.in', 'Surefix Sales');
    $mail->Subject = "Thank You for Contacting Surefix";

    $userContent = "
        <p>Hi " . $esc($full_name) . ",</p>
        <p>Thank you for getting in touch with <b>Surefix</b>. We have received your details and our team will review your enquiry shortly.</p>
        <p>One of our representatives will connect with you within <b>1–2 working days</b>.</p>
        <br>
        <p>Regards,<br><b>Team Surefix</b><br>
        <a href='mailto:sales@surefix.co.in'>sales@surefix.co.in</a> | +91 95797 26091</p>
    ";
    $mail->Body = emailTemplate("Thank You!", $userContent);
    dbg('MAIL: user body built');

    $mail->send();
    dbg('MAIL: USER SENT OK');

    $mailSent = true;

} catch (MailException $ex) {
    $mailError = ($mail && $mail->ErrorInfo) ? $mail->ErrorInfo : $ex->getMessage();
    dbg('MAIL EXCEPTION: ' . $mailError);
} catch (\Throwable $t) {
    $mailError = $t->getMessage() . ' @ line ' . $t->getLine();
    dbg('MAIL THROWABLE: ' . $mailError);
}

dbg('--- MAIL END. sent=' . ($mailSent ? 'YES' : 'NO') . ' ---');

   
/* ============================================================
   RESPONSE
============================================================ */
if ($mailSent) {
    dbg('RESPONSE: success');
    echo json_encode(["status" => "success", "message" => "Thank you! Your details have been submitted."]);
} elseif (DEBUG_MODE) {
    dbg('RESPONSE: mail error surfaced');
    echo json_encode(["status" => "error", "message" => "MAIL ERROR: " . $mailError]);
} else {
    dbg('RESPONSE: success despite mail failure');
    echo json_encode(["status" => "success", "message" => "Your details have been recorded. We will contact you shortly."]);
}

dbg('========== END ==========');