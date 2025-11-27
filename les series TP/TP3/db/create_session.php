<?php
require_once __DIR__ . '/db_connect.php';
$pdo = db_connect();
if (!$pdo) die("DB connection failed.");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Send POST course_id, group_id, opened_by";
    exit;
}

$course_id = $_POST['course_id'] ?? null;
$group_id = $_POST['group_id'] ?? null;
$opened_by = $_POST['opened_by'] ?? null;

$sql = "INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status) VALUES (?, ?, CURDATE(), ?, 'open')";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $group_id, $opened_by]);
echo "Session created. ID = " . $pdo->lastInsertId();
