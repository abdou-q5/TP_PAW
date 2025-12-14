<?php
// api/add_student.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';

$input = json_decode(file_get_contents('php://input'), true);

$studentId = $input['studentId'] ?? null;
$lastName  = $input['lastName'] ?? null;
$firstName = $input['firstName'] ?? null;
$email     = $input['email'] ?? null;

if (!$studentId || !$lastName || !$firstName || !$email) {
    http_response_code(400);
    echo json_encode(['error' => 'missing_fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO students (student_id, last_name, first_name, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$studentId, $lastName, $firstName, $email]);
    $id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'id' => $id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'db_error', 'message' => $e->getMessage()]);
}
