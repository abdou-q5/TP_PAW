<?php
// backend/admin/add_course.php
require_once __DIR__.'/../functions.php';
require_role('admin');
$input = json_decode(file_get_contents('php://input'), true);
$title = $input['title'] ?? null;
$prof_id = $input['professor_id'] ?? null;
if (!$title) json_response(['error'=>'title required']);
$stmt = $pdo->prepare('INSERT INTO courses (title,professor_id) VALUES (?,?)');
$stmt->execute([$title,$prof_id]);
json_response(['ok'=>true,'id'=>$pdo->lastInsertId()]);