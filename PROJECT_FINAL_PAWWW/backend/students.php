<?php
include 'config.php';

$action = $_GET['action'] ?? '';

if($action == 'list'){
    $stmt = $conn->query("SELECT id, name, email FROM users WHERE role='student'");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);
}

// لاحقاً يمكن تضيف add/update/delete
?>
