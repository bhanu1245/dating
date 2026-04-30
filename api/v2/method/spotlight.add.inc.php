<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

header('Content-Type: application/json');
$result = array("error" => true, "msg" => "failed", "data" => new stdClass());

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $resultData = array("error" => true);

    $account = new account($dbo, $accountId);
    $accountInfo = $account->get();

    $settings = new settings($dbo);
    $config = $settings->get();

    $arr = $config['defaultSpotlightCost'];
    $spotlightCost = $arr['intValue'];

    if (!$accountInfo['error']) {

        if ($accountInfo['balance'] >= $spotlightCost) {

            if (auth::isSession()) {

                auth::setCurrentUserBalance($accountInfo['balance'] - $spotlightCost);
            }

            $account->setBalance($accountInfo['balance'] - $spotlightCost);

            $payments = new payments($dbo);
            $payments->setRequestFrom($accountId);
            $payments->create(PA_BUY_SPOTLIGHT, PT_CREDITS, $spotlightCost, 0);
            unset($payments);

            $spotlight = new spotlight($dbo);
            $spotlight->setRequestFrom($accountId);
            $resultData = $spotlight->add($accountId);
            unset($spotlight);
        }
    }
    if (isset($resultData['error_code'])) unset($resultData['error_code']);
    if (!isset($resultData['balance'])) $resultData['balance'] = $account->getBalance();
    $result = array(
        "error" => !empty($resultData['error']),
        "msg" => !empty($resultData['error']) ? "failed" : "success",
        "data" => $resultData
    );
    echo json_encode($result);
    exit;
}

echo json_encode($result);
exit;
