<?php
include 'config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if($action == 'mark'){
    $session_id = $_POST['session_id'];
    $student_id = $_POST['student_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO attendance_records (session_id, student_id, status) VALUES (?,?,?)");
    if($stmt->execute([$session_id, $student_id, $status])){
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false]);
    }
}

if($action == 'list'){
    $session_id = $_GET['session_id'];
    $stmt = $conn->prepare("SELECT ar.id, u.name, ar.status FROM attendance_records ar JOIN users u ON ar.student_id=u.id WHERE ar.session_id=?");
    $stmt->execute([$session_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>
