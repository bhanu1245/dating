<?php

/*!
 * https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2024 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */


if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : '';
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $login = isset($_POST['login']) ? $_POST['login'] : '';

    $currentPassword = isset($_POST['currentPassword']) ? $_POST['currentPassword'] : '';
    $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';

    $currentPassword = helper::clearText($currentPassword);
    $currentPassword = helper::escapeText($currentPassword);

    $newPassword = helper::clearText($newPassword);
    $newPassword = helper::escapeText($newPassword);

    $login = helper::clearText($login);
    $login = helper::escapeText($login);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    $account = new account($dbo, $accountId);
    $accountInfo = $account->get();

    if (!$accountInfo['error']) {

        if ($accountInfo['account_free'] != 0) {

            $result = $account->createPassword($login, $newPassword);

        } else {

            $result = $account->setPassword($currentPassword, $newPassword);
        }
    }


    echo json_encode($result);
    exit;
}
