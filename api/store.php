<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
requireLogin();
try{
  $pdo=db(); $method=$_SERVER['REQUEST_METHOD']; $uid=(int)$_SESSION['user_id'];
  if($method==='GET'){
    $rows=$pdo->query("SELECT data_key,data_value FROM erp_store ORDER BY data_key")->fetchAll();
    $out=[]; foreach($rows as $r) $out[$r['data_key']]=$r['data_value']; jsonResponse(true,$out);
  }
  $in=json_decode(file_get_contents('php://input'),true) ?: [];
  if($method==='POST'){
    $key=trim((string)($in['key']??'')); $value=$in['value']??null;
    if(!preg_match('/^jkt_[a-z0-9_]+$/i',$key)) jsonResponse(false,null,'Invalid storage key',422);
    $json=json_encode($value,JSON_UNESCAPED_UNICODE);
    $st=$pdo->prepare("INSERT INTO erp_store(data_key,data_value,updated_by) VALUES(?,?,?) ON DUPLICATE KEY UPDATE data_value=VALUES(data_value),updated_by=VALUES(updated_by),updated_at=CURRENT_TIMESTAMP");
    $st->execute([$key,$json,$uid]); jsonResponse(true,null,'Saved');
  }
  if($method==='DELETE'){
    $key=trim((string)($in['key']??'')); if($key==='') $pdo->exec("DELETE FROM erp_store"); else $pdo->prepare("DELETE FROM erp_store WHERE data_key=?")->execute([$key]); jsonResponse(true,null,'Deleted');
  }
  jsonResponse(false,null,'Method not allowed',405);
}catch(Throwable $e){ error_log($e->getMessage()); jsonResponse(false,null,'Server error',500); }
