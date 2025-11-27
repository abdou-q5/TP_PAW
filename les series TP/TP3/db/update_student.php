<?php
require_once __DIR__ . '/db_connect.php';
$pdo = db_connect();
if (!$pdo) die("DB connection failed.");

// expecting POST id, fullname, matricule, group_id
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Send POST id, fullname, matricule, group_id";
    exit;
}

$id = intval($_POST['id'] ?? 0);
$fullname = trim($_POST['fullname'] ?? '');
$matricule = trim($_POST['matricule'] ?? '');
$group_id = trim($_POST['group_id'] ?? '');

if ($id <= 0 || $fullname === '' || $matricule === '') die("Invalid data.");

$sql = "UPDATE students SET fullname = ?, matricule = ?, group_id = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$fullname, $matricule, $group_id, $id]);
echo "Student updated.";
