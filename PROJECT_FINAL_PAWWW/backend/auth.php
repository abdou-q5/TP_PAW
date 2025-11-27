<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($user && password_verify($password, $user['password'])){
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    echo json_encode(['success'=>true,'role'=>$user['role']]);
} else {
    echo json_encode(['success'=>false,'message'=>'Invalid username or password']);
}
?>
