<?php
if (!defined("APP_SIGNATURE")) { header("Location: /"); exit; }
header('Content-Type: application/json');
$result = ["error"=>true,"msg"=>"failed","data"=>new stdClass()];
try {
 if (empty($_POST)) throw new Exception('Empty request');
 $accountId = helper::clearInt($_POST['accountId'] ?? 0);
 $accessToken = $_POST['accessToken'] ?? '';
 $profileId = helper::clearInt($_POST['profileId'] ?? 0);
 $auth = new auth($dbo); if (!$auth->authorize($accountId, $accessToken)) api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
 if ($accountId == $profileId) throw new Exception('Invalid profile');
 $profile = new profile($dbo, $profileId); $profile->setRequestFrom($accountId);
 $data = $profile->like($accountId); unset($data['error_code']);
 $result = ["error"=>!empty($data['error']),"msg"=>!empty($data['error'])?($data['msg']??'failed'):'success',"data"=>$data];
} catch (Throwable $e) { error_log('profile.like: '.$e->getMessage()); $result['msg']=$e->getMessage(); }
echo json_encode($result); exit;
