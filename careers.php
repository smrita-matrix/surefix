<?php
/* ============================================================
   BOOTSTRAP
============================================================ */
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

define('DBG_FILE', __DIR__ . '/careers_debug.log');

function dbg($msg) {
    @file_put_contents(DBG_FILE, '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL, FILE_APPEND);
}

dbg('========== BOOT ==========');
dbg('PHP version: ' . PHP_VERSION);
dbg('Script: ' . __FILE__);
dbg('METHOD: ' . ($_SERVER['REQUEST_METHOD'] ?? 'n/a'));
dbg('IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'n/a'));

register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        dbg('FATAL: ' . $err['message'] . ' @ ' . $err['file'] . ':' . $err['line']);
        if (!headers_sent()) header('Content-Type: application/json');
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

/* Resume upload settings */
define('UPLOAD_DIR', __DIR__ . '/uploads/resumes/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB

dbg('CONFIG loaded. DB=' . DB_NAME . ' SMTP=' . SMTP_HOST);

/* ============================================================
   METHOD GUARD
============================================================ */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    dbg('ABORT: non-POST, redirecting');
    header('Location: /careers.html');
    exit;
}

header('Content-Type: application/json');
dbg('POST confirmed. JSON header sent.');
dbg('RAW POST KEYS: ' . implode(', ', array_keys($_POST)));
dbg('RAW FILE KEYS: ' . implode(', ', array_keys($_FILES)));

/* ============================================================
   COLLECT DATA
============================================================ */
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$city       = trim($_POST['city'] ?? '');
$position   = trim($_POST['position'] ?? '');
$intro      = trim($_POST['intro'] ?? '');
$recaptcha  = trim($_POST['g-recaptcha-response'] ?? '');

dbg('FIELD first_name=' . $first_name);
dbg('FIELD last_name=' . $last_name);
dbg('FIELD email=' . $email);
dbg('FIELD phone=' . $phone);
dbg('FIELD city=' . $city);
dbg('FIELD position=' . $position);
dbg('FIELD intro length=' . strlen($intro));
dbg('FIELD recaptcha length=' . strlen($recaptcha));

$errors = [];

/* ============================================================
   FIELD VALIDATION
============================================================ */
dbg('--- VALIDATION START ---');

if ($first_name === '' || !preg_match("/^[A-Za-z\s.'-]{2,50}$/", $first_name)) {
    $errors['first_name'] = "Enter a valid first name (letters only).";
    dbg('FAIL: first_name');
} else { dbg('OK: first_name'); }

if ($last_name === '' || !preg_match("/^[A-Za-z\s.'-]{2,50}$/", $last_name)) {
    $errors['last_name'] = "Enter a valid last name (letters only).";
    dbg('FAIL: last_name');
} else { dbg('OK: last_name'); }

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Enter a valid email address.";
    dbg('FAIL: email');
} else { dbg('OK: email'); }

if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
    $errors['phone'] = "Phone number must be 10 to 15 digits.";
    dbg('FAIL: phone');
} else { dbg('OK: phone'); }

if ($city === '' || !preg_match("/^[A-Za-z\s.,'-]{2,50}$/", $city)) {
    $errors['city'] = "Enter a valid city name (letters only).";
    dbg('FAIL: city');
} else { dbg('OK: city'); }

if ($position === '' || !preg_match("/^[A-Za-z0-9\s.,\/&'()-]{2,100}$/", $position)) {
    $errors['position'] = "Enter a valid position.";
    dbg('FAIL: position');
} else { dbg('OK: position'); }

if ($intro !== '' && strlen($intro) > 2000) {
    $errors['intro'] = "Intro must be under 2000 characters.";
    dbg('FAIL: intro too long');
} else { dbg('OK: intro'); }

dbg('--- VALIDATION END. errors=' . count($errors) . ' ---');

/* ============================================================
   RESUME FILE VALIDATION
============================================================ */
dbg('--- FILE VALIDATION START ---');

$resumeOk       = false;
$resumeTmp      = '';
$resumeOrigName = '';
$resumeNewName  = '';
$resumeMime     = '';

if (!isset($_FILES['resume']) || $_FILES['resume']['error'] === UPLOAD_ERR_NO_FILE) {
    $errors['resume'] = "Please attach your resume.";
    dbg('FILE FAIL: no file uploaded');
} elseif ($_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
    $errors['resume'] = "File upload failed. Please try again.";
    dbg('FILE FAIL: upload error code=' . $_FILES['resume']['error']);
} else {
    $resumeTmp      = $_FILES['resume']['tmp_name'];
    $resumeOrigName = basename($_FILES['resume']['name']);
    $resumeSize     = (int) $_FILES['resume']['size'];
    $resumeExt      = strtolower(pathinfo($resumeOrigName, PATHINFO_EXTENSION));

    dbg('FILE: name=' . $resumeOrigName . ' size=' . $resumeSize . ' ext=' . $resumeExt);

    if ($resumeSize > MAX_FILE_SIZE) {
        $errors['resume'] = "Resume must be under 5 MB.";
        dbg('FILE FAIL: too large');
    } elseif (!in_array($resumeExt, ['pdf', 'doc', 'docx'], true)) {
        $errors['resume'] = "Only PDF, DOC or DOCX files are allowed.";
        dbg('FILE FAIL: bad extension');
    } else {
        /* Verify real MIME type, not just the extension */
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $resumeMime = finfo_file($finfo, $resumeTmp);
        finfo_close($finfo);
        dbg('FILE: detected mime=' . $resumeMime);

        $allowedMime = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/octet-stream',
            'application/zip'
        ];

        if (!in_array($resumeMime, $allowedMime, true)) {
            $errors['resume'] = "Invalid file type. Upload a PDF, DOC or DOCX.";
            dbg('FILE FAIL: mime not allowed');
        } else {
            $safeName      = preg_replace('/[^A-Za-z0-9]/', '_', $first_name . '_' . $last_name);
            $resumeNewName = $safeName . '_' . date('Ymd_His') . '_' . mt_rand(1000, 9999) . '.' . $resumeExt;
            $resumeOk      = true;
            dbg('FILE OK. target name=' . $resumeNewName);
        }
    }
}

dbg('--- FILE VALIDATION END ---');

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
   MOVE UPLOADED FILE
============================================================ */
dbg('--- FILE MOVE START ---');

if (!is_dir(UPLOAD_DIR)) {
    if (!@mkdir(UPLOAD_DIR, 0755, true)) {
        dbg('FILE MOVE FAIL: could not create ' . UPLOAD_DIR);
        echo json_encode([
            "status"  => "error",
            "message" => DEBUG_MODE ? "Could not create upload directory." : "Upload error."
        ]);
        exit;
    }
    dbg('FILE: created upload dir ' . UPLOAD_DIR);
}

$resumePath = UPLOAD_DIR . $resumeNewName;

if (!move_uploaded_file($resumeTmp, $resumePath)) {
    dbg('FILE MOVE FAIL: could not move to ' . $resumePath);
    echo json_encode([
        "status"  => "error",
        "message" => DEBUG_MODE ? "Could not save resume file." : "Upload error."
    ]);
    exit;
}

dbg('FILE MOVED OK -> ' . $resumePath);
dbg('--- FILE MOVE END ---');

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

$tblCheck = $conn->query("SHOW TABLES LIKE 'career_applications'");
if (!$tblCheck || $tblCheck->num_rows === 0) {
    dbg('DB FAIL: table career_applications does NOT exist');
    echo json_encode([
        "status"  => "error",
        "message" => DEBUG_MODE ? "Table 'career_applications' does not exist. Run the CREATE TABLE query." : "Database error."
    ]);
    exit;
}
dbg('DB: table career_applications exists');

$sql = "INSERT INTO career_applications
        (first_name, last_name, email, phone, city, position, intro, resume_file, resume_original_name, ip_address, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
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
$stmt->bind_param(
    "ssssssssss",
    $first_name, $last_name, $email, $phone, $city,
    $position, $intro, $resumeNewName, $resumeOrigName, $ip
);
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

/* ============================================================
   MAIL
============================================================ */
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
    $mail->SMTPSecure  = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port        = 465;
    $mail->Timeout     = 20;
    $mail->isHTML(true);
    $mail->CharSet     = "UTF-8";
    $mail->SMTPOptions = [
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]
    ];
    dbg('MAIL: SMTP configured host=' . SMTP_HOST . ' port=465 SMTPS');

    /* ---- ADMIN MAIL (with resume attached) ---- */
    $mail->setFrom(SMTP_USER, 'Surefix Careers');
    $mail->addAddress(ADMIN_EMAIL);
    $mail->addReplyTo($email, $first_name . ' ' . $last_name);
    $mail->Subject = "New Job Application - " . $position . " | Surefix";

    $mail->addAttachment($resumePath, $resumeOrigName);
    dbg('MAIL: resume attached -> ' . $resumeOrigName);

    $adminContent = "
        <p><b>First Name:</b> " . $esc($first_name) . "</p>
        <p><b>Last Name:</b> " . $esc($last_name) . "</p>
        <p><b>Email:</b> " . $esc($email) . "</p>
        <p><b>Phone Number:</b> " . $esc($phone) . "</p>
        <p><b>City:</b> " . $esc($city) . "</p>
        <p><b>Position Applying For:</b> " . $esc($position) . "</p>
        <hr>
        <p><b>Intro / Why should we hire you:</b><br>"
        . ($intro !== '' ? nl2br($esc($intro)) : '<i>Not provided</i>') . "</p>
        <hr>
        <p><b>Resume:</b> " . $esc($resumeOrigName) . " <i>(attached to this email)</i></p>
        <p style='font-size:12px;color:#888'>Submitted on " . date('d M Y, h:i A') . " | IP: " . $esc($ip) . "</p>
    ";
    $mail->Body = emailTemplate("New Job Application Received", $adminContent);
    dbg('MAIL: admin body built');

    $mail->send();
    dbg('MAIL: ADMIN SENT OK');

    /* ---- USER ACKNOWLEDGEMENT (no attachment) ---- */
    $mail->clearAddresses();
    $mail->clearReplyTos();
    $mail->clearAttachments();
    dbg('MAIL: recipients + attachments cleared');

    $mail->addAddress($email, $first_name . ' ' . $last_name);
    $mail->setFrom(SMTP_USER, 'Surefix');
    $mail->addReplyTo('sales@surefix.co.in', 'Surefix HR');
    $mail->Subject = "Thank You for Applying to Surefix";

    $userContent = "
        <p>Hi " . $esc($first_name) . ",</p>
        <p>Thank you for applying for the position of <b>" . $esc($position) . "</b> at <b>Surefix</b>.</p>
        <p>We have received your application along with your resume. Our HR team will review your profile and get in touch if your background matches our current requirements.</p>
        <p>Please note that due to the volume of applications, only shortlisted candidates will be contacted.</p>
        <br>
        <p>Regards,<br><b>Team Surefix</b><br>
        <a href='mailto:sales@surefix.co.in'>sales@surefix.co.in</a> | +91 95797 26091</p>
    ";
    $mail->Body = emailTemplate("Application Received!", $userContent);
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
    echo json_encode(["status" => "success", "message" => "Thank you! Your application has been submitted."]);
} elseif (DEBUG_MODE) {
    dbg('RESPONSE: mail error surfaced');
    echo json_encode(["status" => "error", "message" => "MAIL ERROR: " . $mailError]);
} else {
    dbg('RESPONSE: success despite mail failure');
    echo json_encode(["status" => "success", "message" => "Your application has been recorded. We will contact you shortly."]);
}

dbg('========== END ==========');