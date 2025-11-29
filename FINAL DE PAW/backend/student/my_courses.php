<?php
// backend/student/my_courses.php
require_once __DIR__ . '/../functions.php';
require_roles(['student']);

$uid = $_SESSION['user']['id'];

// récupérer les cours de l'étudiant
$stmt = $pdo->prepare(
    'SELECT c.id, c.title, u.fullname AS professor
     FROM courses c
     LEFT JOIN users u ON u.id = c.professor_id
     JOIN enrollments e ON e.course_id = c.id
     WHERE e.student_id = ?'
);
$stmt->execute([$uid]);

$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// pour chaque cours, récupérer présences
foreach ($courses as &$c) {
    $s = $pdo->prepare(
        'SELECT date, presence, participation
         FROM attendance
         WHERE course_id = ? AND student_id = ?
         ORDER BY date DESC'
    );
    $s->execute([$c['id'], $uid]);
    $c['attendance'] = $s->fetchAll(PDO::FETCH_ASSOC);
}

json_response(['courses' => $courses]);
