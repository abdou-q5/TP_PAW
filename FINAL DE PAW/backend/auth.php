<?php
// backend/auth.php
session_start();
require_once __DIR__.'/functions.php';


$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
// login
$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';
if (!$username || !$password) json_response(['error'=>'missing']);


$stmt = $pdo->prepare('SELECT id, username, password, role, fullname FROM users WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$user = $stmt->fetch();
if ($user && password_verify($password, $user['password'])) {
unset($user['password']);
$_SESSION['user'] = $user;
json_response(['ok'=>true, 'user'=>$user]);
}
json_response(['error'=>'invalid credentials']);
}


if ($method === 'GET' && isset($_GET['action']) && $_GET['action']==='logout'){
session_destroy();
json_response(['ok'=>true]);
}