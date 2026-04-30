<?php

if (!defined("APP_SIGNATURE")) {
    header("Location: /");
    exit;
}

header('Content-Type: application/json');

$result = array("error" => true, "msg" => "failed", "data" => new stdClass());

try {
    if (empty($_POST)) {
        throw new Exception('Empty request');
    }

    $accountId = helper::clearInt($_POST['accountId'] ?? 0);
    $accessToken = helper::escapeText(helper::clearText($_POST['accessToken'] ?? ''));

    $auth = new auth($dbo);
    if (!$auth->authorize($accountId, $accessToken)) {
        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    if (!isset($_FILES['uploaded_video_file']) || $_FILES['uploaded_video_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No video uploaded');
    }

    if ($_FILES['uploaded_video_file']['size'] > VIDEO_FILE_MAX_SIZE) {
        throw new Exception('Exceeded file size limit.');
    }

    $ext = strtolower((string) pathinfo($_FILES['uploaded_video_file']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, array('mp4', 'webm'), true)) {
        throw new Exception('Unsupported video format. Use mp4 or webm.');
    }

    if (!is_dir(TEMP_PATH) && !@mkdir(TEMP_PATH, 0775, true)) {
        throw new Exception('Temp directory is not writable');
    }

    $videoTmpPath = TEMP_PATH.uniqid('video_', true).'.'.$ext;
    if (!@move_uploaded_file($_FILES['uploaded_video_file']['tmp_name'], $videoTmpPath)) {
        throw new Exception('Failed to move uploaded video');
    }

    $cdn = new cdn($dbo);
    $videoUpload = $cdn->uploadVideo($videoTmpPath);

    if ($videoUpload['error']) {
        throw new Exception('Failed to save video');
    }

    $imgFileUrl = '';

    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK) {

        $imgExt = strtolower((string) pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION));
        if (in_array($imgExt, array('jpg', 'jpeg', 'png'), true)) {

            $imgTmpPath = TEMP_PATH.uniqid('video_thumb_', true).'.'.$imgExt;
            if (@move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $imgTmpPath)) {
                $imgUpload = $cdn->uploadMyPhoto($imgTmpPath);
                if (!$imgUpload['error']) {
                    $imgFileUrl = $imgUpload['fileUrl'];
                }
            }
        }
    }

    $result = array(
        "error" => false,
        "msg" => "success",
        "data" => array(
            "imgFileUrl" => $imgFileUrl,
            "videoFileUrl" => $videoUpload['fileUrl'],
            "videoUrl" => $videoUpload['fileUrl'],
            "videoMimeType" => ($ext === 'webm' ? 'video/webm' : 'video/mp4')
        )
    );

} catch (Throwable $e) {
    $result['error'] = true;
    $result['msg'] = $e->getMessage();
}

echo json_encode($result);
exit;
