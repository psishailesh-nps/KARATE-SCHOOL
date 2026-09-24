<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
startSecureSession();
try {
  $pdo=db(); $method=$_SERVER['REQUEST_METHOD']; $input=json_decode(file_get_contents('php://input'),true) ?: [];
  if($method==='GET'){
    if(!empty($_SESSION['user_id'])) jsonResponse(true,['id'=>(int)$_SESSION['user_id'],'username'=>$_SESSION['username'],'name'=>$_SESSION['name'],'role'=>$_SESSION['role']]);
    jsonResponse(false,null,'Not authenticated',401);
  }
  if($method==='POST'){
    $u=trim((string)($input['username']??'')); $p=(string)($input['password']??''); $role=trim((string)($input['role']??''));
    if($u===''||$p==='') jsonResponse(false,null,'Username and password are required',422);
    $st=$pdo->prepare("SELECT id,username,name,password_hash,role,status FROM users WHERE username=? LIMIT 1"); $st->execute([$u]); $row=$st->fetch();
    if(!$row || $row['status']!=='Active' || !password_verify($p,$row['password_hash']) || ($role!=='' && $row['role']!==$role)) jsonResponse(false,null,'Invalid username, password or role',401);
    session_regenerate_id(true); $_SESSION['user_id']=(int)$row['id']; $_SESSION['username']=$row['username']; $_SESSION['name']=$row['name']; $_SESSION['role']=$row['role'];
    $pdo->prepare("INSERT INTO audit_logs(user_id,action,entity,ip_address) VALUES(?,?,?,?)")->execute([(int)$row['id'],'LOGIN','auth',$_SERVER['REMOTE_ADDR']??null]);
    jsonResponse(true,['id'=>(int)$row['id'],'username'=>$row['username'],'name'=>$row['name'],'role'=>$row['role']],'Login successful');
  }
  if($method==='DELETE'){
    $_SESSION=[]; session_destroy(); jsonResponse(true,null,'Logged out');
  }
  jsonResponse(false,null,'Method not allowed',405);
}catch(Throwable $e){ error_log($e->getMessage()); jsonResponse(false,null,'Server error',500); }
