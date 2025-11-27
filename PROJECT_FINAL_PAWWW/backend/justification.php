<?php
include 'config.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD']=='POST'){
    $student_id = $_POST['student_id'];
    $session_id = $_POST['session_id'];
    $file = $_FILES['file'];

    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($file['name']);
    if(move_uploaded_file($file['tmp_name'], $target_file)){
        $stmt = $conn->prepare("INSERT INTO justifications (student_id, session_id, file_path) VALUES (?,?,?)");
        $stmt->execute([$student_id, $session_id, $target_file]);
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false]);
    }
}
?>
