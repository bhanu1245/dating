<?php

ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);

try {

    require_once dirname(__DIR__, 3) . '/sys/config/db.inc.php';
    require_once dirname(__DIR__, 3) . '/sys/config/constants.inc.php';
    require_once dirname(__DIR__, 3) . '/sys/class/class.helper.inc.php';
    require_once dirname(__DIR__, 3) . '/sys/class/class.auth.inc.php';
    require_once dirname(__DIR__, 3) . '/sys/class/class.account.inc.php';

    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    $dbo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Allowed extensions
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    // ✅ Validate file
    if (!isset($_FILES['uploaded_file'])) {
        throw new Exception("No file received");
    }

    if ($_FILES['uploaded_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Upload error code: " . $_FILES['uploaded_file']['error']);
    }

    if (!is_uploaded_file($_FILES['uploaded_file']['tmp_name'])) {
        throw new Exception("Invalid upload");
    }

    // ✅ Extension check
    $ext = strtolower(pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        $ext = "jpg";
    }

    // ✅ Upload directory
    $upload_dir = dirname(__DIR__, 3) . "/uploads/photos/";

    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception("Failed to create upload folder");
        }
    }

    if (!is_writable($upload_dir)) {
        throw new Exception("Upload folder not writable");
    }

    // ✅ Generate file
    $file_hash = sha1_file($_FILES['uploaded_file']['tmp_name']);
    $file_name = $file_hash . "." . $ext;
    $file_path = $upload_dir . $file_name;

    // ✅ Move file
    if (!move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
        throw new Exception("Failed to move uploaded file");
    }

    if (!file_exists($file_path)) {
        throw new Exception("File not saved");
    }

    $accountId = intval($_POST['accountId'] ?? 0);
    $accessToken = $_POST['accessToken'] ?? "";

    if ($accountId == 0 || empty($accessToken)) {
        throw new Exception("Missing account data");
    }

    $auth = new auth($dbo);
    if (!$auth->authorize($accountId, $accessToken)) {
        throw new Exception("Authorization failed");
    }

    $photo_url = "/uploads/photos/" . $file_name;

    $stmt = $dbo->prepare("
    UPDATE users 
    SET 
        lowPhotoUrl = ?, 
        normalPhotoUrl = ?, 
        bigPhotoUrl = ?, 
        registrationComplete = 1
    WHERE id = ?
    ");

    $stmt->execute([$photo_url, $photo_url, $photo_url, $accountId]);

    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === $accountId) {

        auth::setCurrentUserPhotoUrl($photo_url);
        auth::setRegistrationComplete(1);
    }

    ob_end_clean();

    echo json_encode([
        "error" => false,
        "msg" => "Upload success",
        "photoUrl" => $photo_url,
        "originPhotoUrl" => $photo_url,
        "normalPhotoUrl" => $photo_url,
        "bigPhotoUrl" => $photo_url,
        "lowPhotoUrl" => $photo_url,
        "registrationComplete" => 1
    ]);

} catch (Throwable $e) {

    ob_end_clean();

    echo json_encode([
        "error" => true,
        "msg" => $e->getMessage()
    ]);
}

exit;
