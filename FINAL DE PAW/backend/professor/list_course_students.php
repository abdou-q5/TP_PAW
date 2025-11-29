<?php
// backend/professor/list_course_students.php
require_once __DIR__.'/../functions.php';
require_roles(['professor']);
$course_id = (int)($_GET['course_id'] ?? 0);
if (!$course_id) json_response(['error'=>'course_id']);
$stmt = $pdo->prepare('SELECT u.id,u.username,u.fullname FROM users u JOIN enrollments e ON e.student_id = u.id WHERE e.course_id = ?');
$stmt->execute([$course_id]);
json_response(['students'=>$stmt->fetchAll()]);