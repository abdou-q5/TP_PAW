<?php
require_once __DIR__ . '/db_connect.php';
$pdo = db_connect();
if (!$pdo) die("DB connection failed.");

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid id.");

$sql = "DELETE FROM students WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
echo "Student deleted.";
