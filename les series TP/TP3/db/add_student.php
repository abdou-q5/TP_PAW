<?php
require_once __DIR__ . '/db_connect.php';
$pdo = db_connect();
if (!$pdo) die("DB connection failed.");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Send POST with fullname, matricule, group_id";
    exit;
}

$fullname = trim($_POST['fullname'] ?? '');
$matricule = trim($_POST['matricule'] ?? '');
$group_id = trim($_POST['group_id'] ?? '');

if ($fullname === '' || $matricule === '') die("fullname and matricule required.");

$sql = "INSERT INTO students (fullname, matricule, group_id) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([$fullname, $matricule, $group_id]);
    echo "Student added. ID = " . $pdo->lastInsertId();
} catch (PDOException $e) {
    if ($e->getCode() == 23000) echo "Matricule already exists.";
    else echo "Error: " . $e->getMessage();
}
