<?php
// backend/admin/add_user.php
require_once __DIR__.'/../functions.php';
require_role('admin');


$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? null;
$pass = $input['password'] ?? 'default123';
$role = $input['role'] ?? 'student';
$fullname = $input['fullname'] ?? null;


if (!$username) json_response(['error'=>'username required']);
$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (username,password,role,fullname) VALUES (?,?,?,?)');
try{
$stmt->execute([$username,$hash,$role,$fullname]);
json_response(['ok'=>true,'id'=>$pdo->lastInsertId()]);
}catch(Exception $e){
json_response(['error'=>$e->getMessage()]);
}