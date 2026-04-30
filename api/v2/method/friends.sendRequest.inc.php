<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

header('Content-Type: application/json');

$result = array("error" => true, "msg" => "failed", "data" => new stdClass());

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $profileId = isset($_POST['profileId']) ? $_POST['profileId'] : 0;

    $accountId = helper::clearInt($accountId);
    $profileId = helper::clearInt($profileId);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    if ($accountId == $profileId) {

        echo json_encode($result);
        exit;
    }

    $profile = new profile($dbo, $profileId);
    $profile->setRequestFrom($accountId);

    $result = $profile->addFollower($accountId);

    if (isset($result['error_code'])) unset($result['error_code']);
    if (!$result['error']) $result = array('error' => false, 'msg' => 'success', 'data' => $result);
    echo json_encode($result);
    exit;
}

echo json_encode($result);
exit;
