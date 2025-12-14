<?php
// api/get_students.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->query("SELECT id, student_id, last_name, first_name, email FROM students ORDER BY last_name, first_name");
    $students = $stmt->fetchAll();
    echo json_encode($students);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'failed', 'message' => $e->getMessage()]);
}
