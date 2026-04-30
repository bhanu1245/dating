<?php

/*!
 * https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2022 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

header('Content-Type: application/json');
$result = array("error" => true, "msg" => "failed", "data" => new stdClass());

try {
if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $credits = isset($_POST['credits']) ? $_POST['credits'] : 0;
    $upgradeType = isset($_POST['upgradeType']) ? $_POST['upgradeType'] : 0;

    $credits = helper::clearInt($credits);
    $upgradeType = helper::clearInt($upgradeType);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $resultData = array("error" => true);

    $account = new account($dbo, $accountId);

    $balance = $account->getBalance();

    if ($balance >= $credits) {

        switch ($upgradeType) {

            case PA_BUY_VERIFIED_BADGE: {

                $account->setBalance($account->getBalance() - $credits);

                $resultData = $account->setVerify(1);

                break;
            }

            case PA_BUY_GHOST_MODE: {

                $account->setBalance($account->getBalance() - $credits);

                $resultData = $account->setGhost(1);

                break;
            }

            case PA_BUY_DISABLE_ADS: {

                $account->setBalance($account->getBalance() - $credits);

                $resultData = $account->setAdmob(1);

                break;
            }

            case PA_BUY_PRO_MODE: {

                $account->setBalance($account->getBalance() - $credits);

                $resultData = $account->setPro(1);

                break;
            }

            case PA_BUY_MESSAGE_PACKAGE: {

                $account->setBalance($account->getBalance() - $credits);

                $resultData = $account->setFreeMessagesCount($account->getFreeMessagesCount() + 100);

                break;
            }

            default: {

                break;
            }
        }

        if (empty($resultData['error'])) {

            $payments = new payments($dbo);
            $payments->setRequestFrom($accountId);
            $payments->create($upgradeType, PT_CREDITS, $credits);
            unset($payments);
        }
    }
    if (isset($resultData['error_code'])) unset($resultData['error_code']);
    $resultData['balance'] = $account->getBalance();
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
} catch (Throwable $e) { error_log("account.upgrade: ".$e->getMessage()); echo json_encode(["error"=>true,"msg"=>$e->getMessage(),"data"=>["balance"=>isset($account)?$account->getBalance():0]]); exit; }
