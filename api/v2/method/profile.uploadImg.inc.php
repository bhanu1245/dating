<?php

if (!defined("APP_SIGNATURE")) {
    header("Location: /");
    exit;
}

header('Content-Type: application/json');

$result = ["error" => true, "msg" => "failed", "data" => new stdClass()];

try {
    if (empty($_POST)) {
        echo json_encode($result);
        exit;
    }

    $accountId = helper::clearInt($_POST['accountId'] ?? 0);
    $accessToken = helper::escapeText(helper::clearText($_POST['accessToken'] ?? ''));

    $auth = new auth($dbo);
    if (!$auth->authorize($accountId, $accessToken)) {
        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    if (!isset($_FILES['uploaded_file']) || $_FILES['uploaded_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded');
    }

    $imglib = new imglib($dbo);
    if (!$imglib->isImageFile($_FILES['uploaded_file']['tmp_name'], true, false)) {
        throw new Exception('Invalid image format');
    }

    $upload = $imglib->newProfilePhoto($_FILES['uploaded_file']['tmp_name']);
    if ($upload['error']) {
        throw new Exception('Failed to process image');
    }

    $profile = new profile($dbo, $accountId);
    $profileInfo = $profile->getVeryShort();
    @unlink(rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/'.trim(PHOTO_PATH, '/').'/'.basename($profileInfo['normalPhotoUrl']));
    @unlink(rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/'.trim(PHOTO_PATH, '/').'/'.basename($profileInfo['bigPhotoUrl']));
    @unlink(rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/'.trim(PHOTO_PATH, '/').'/'.basename($profileInfo['lowPhotoUrl']));

    $account = new account($dbo, $accountId);
    $account->setPhoto($upload);
    $account->setRegistrationComplete(1);

    if (auth::isSession() && auth::getCurrentUserId() == $accountId) {
        auth::setCurrentUserPhotoUrl($upload['normalPhotoUrl']);
        auth::setRegistrationComplete(1);
    }

    $result = [
        "error" => false,
        "msg" => "success",
        "data" => [
            "originPhotoUrl" => $upload['originPhotoUrl'],
            "normalPhotoUrl" => $upload['normalPhotoUrl'],
            "bigPhotoUrl" => $upload['bigPhotoUrl'],
            "lowPhotoUrl" => $upload['lowPhotoUrl'],
            "photoUrl" => $upload['normalPhotoUrl'],
            "registrationComplete" => 1
        ]
    ];
} catch (Throwable $e) {
    $result['error'] = true;
    $result['msg'] = $e->getMessage();
}

echo json_encode($result);
exit;
