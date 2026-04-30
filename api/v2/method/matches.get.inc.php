<?php
header('Content-Type: application/json');
$result=['error'=>true,'msg'=>'failed','data'=>['items'=>[]]];
try {


    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $profileId = isset($_POST['profileId']) ? $_POST['profileId'] : 0;
    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;

    $profileId = helper::clearInt($profileId);
    $itemId = helper::clearInt($itemId);

    $result = array("error" => true,
                    "msg"=>"failed");

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $matches = new matches($dbo, $profileId);
    $matches->setRequestFrom($accountId);

    if ($profileId == $accountId && $itemId == 0) {

        $account = new account($dbo, $accountId);
        $account->setLastMatchesView();
        unset($account);
    }

    $result = $matches->get($itemId);

    unset($result['error_code']);
    $result=['error'=>!empty($result['error']),'msg'=>!empty($result['error'])?($result['msg']??'failed'):'success','data'=>$result];
} catch (Throwable $e) { error_log('matches.get: '.$e->getMessage()); $result['msg']=$e->getMessage(); }
echo json_encode($result);
exit;

