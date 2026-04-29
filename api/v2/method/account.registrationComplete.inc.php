<?php

/*!
*
* https://raccoonsquare.com, https://raccoonjohn.com
* raccoonsquare@gmail.com
*
* Copyright 2012-2026 Demianchuk Dmytro and Raccoon John (raccoonsquare@gmail.com)
*/

if (!empty($_POST)) {

    $accountId = isset($_POST['account_id']) ? $_POST['account_id'] : '';
    $accessToken = isset($_POST['access_token']) ? $_POST['access_token'] : '';

    $age = isset($_POST['age']) ? $_POST['age'] : 0;
    $gender = isset($_POST['gender']) ? $_POST['gender'] : 0;
    $orientation = isset($_POST['orientation']) ? $_POST['orientation'] : 0;


    $age = helper::clearInt($age);
    $gender = helper::clearInt($gender);
    $orientation = helper::clearInt($orientation);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    $account = new account($dbo, $accountId);
    $account->setLastActive();
    $account->setRegistrationComplete(1);

    if ($age > 17 && $age < 111) {

        $account->setAge($age);
    }

    $account->setSex($gender);

    if ($orientation > 0 && $orientation < 5) {

        $account->setSexOrientation($orientation);
    }

    echo json_encode($result);
    exit;
}
