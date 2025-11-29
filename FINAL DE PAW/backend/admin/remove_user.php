<?php
// backend/admin/remove_user.php
require_once __DIR__.'/../functions.php';
require_role('admin');
$input = json_decode(file_get_contents('php://input'), true);
$id = (int)($input['id'] ?? 0);
if (!$id) json_response(['error'=>'id required']);
$stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$id]);
json_response(['ok'=>true]);