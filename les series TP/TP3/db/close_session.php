<?php
require_once __DIR__ . '/db_connect.php';
$pdo = db_connect();
if (!$pdo) die("DB connection failed.");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Send POST session_id";
    exit;
}

$session_id = intval($_POST['session_id'] ?? 0);
if ($session_id <= 0) die("Invalid session_id.");

$sql = "UPDATE attendance_sessions SET status = 'closed' WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$session_id]);

echo "Session closed.";
