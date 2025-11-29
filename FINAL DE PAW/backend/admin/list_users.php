<?php
// backend/admin/list_users.php
require_once __DIR__.'/../functions.php';
require_role('admin');
$stmt = $pdo->query('SELECT id,username,role,fullname FROM users');
$rows = $stmt->fetchAll();
json_response(['ok'=>true,'users'=>$rows]);