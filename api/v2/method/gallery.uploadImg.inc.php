<?php
if (!defined("APP_SIGNATURE")) { header("Location: /"); exit; }
header('Content-Type: application/json');
$result=["error"=>true,"error_code"=>ERROR_UNKNOWN,"error_description"=>"Unknown error","items"=>[]];
try {
 if (empty($_POST)) throw new Exception('Empty request');
 $accountId=helper::clearInt($_POST['accountId']??0); $accessToken=helper::escapeText(helper::clearText($_POST['accessToken']??''));
 $auth=new auth($dbo); if(!$auth->authorize($accountId,$accessToken)) api::printError(ERROR_ACCESS_TOKEN,"Error authorization.");
 if(!isset($_FILES['uploaded_file'])) throw new Exception('No files uploaded');
 $files=$_FILES['uploaded_file']; $imglib=new imglib($dbo);
 $names=is_array($files['name'])?$files['name']:[$files['name']];
 foreach(array_keys($names) as $i){
   $err=is_array($files['error'])?$files['error'][$i]:$files['error']; if($err!==UPLOAD_ERR_OK) continue;
   $tmp=is_array($files['tmp_name'])?$files['tmp_name'][$i]:$files['tmp_name'];
   $name=is_array($files['name'])?$files['name'][$i]:$files['name'];
   if(!$imglib->isImageFile($tmp,true,false)) continue;
   $r=$imglib->createMyPhoto($tmp,$name); if(!$r['error']) $result['items'][]=$r;
 }
 if(!count($result['items'])) throw new Exception('No valid images uploaded');
 $result['error']=false; $result['error_code']=ERROR_SUCCESS; $result['error_description']='ok';
} catch (Throwable $e){ $result['error']=true; $result['error_description']=$e->getMessage(); }
echo json_encode($result); exit;
