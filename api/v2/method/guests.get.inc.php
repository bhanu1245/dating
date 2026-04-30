<?php
header('Content-Type: application/json');
$result=['error'=>true,'msg'=>'failed','data'=>['items'=>[]]];
try {


    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;

    $itemId = helper::clearInt($itemId);

    $result = array("error" => true,
                    "msg"=>"failed");

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    if ($itemId == 0) {

        $account = new account($dbo, $accountId);
        $account->setLastGuestsView();
        unset($account);
    }

    $guests = new guests($dbo, $accountId);
    $guests->setRequestFrom($accountId);
    $result = $guests->get($itemId);

    unset($result['error_code']);
    $result=['error'=>!empty($result['error']),'msg'=>!empty($result['error'])?($result['msg']??'failed'):'success','data'=>$result];
} catch (Throwable $e) { error_log('guests.get: '.$e->getMessage()); $result['msg']=$e->getMessage(); }
echo json_encode($result);
exit;

