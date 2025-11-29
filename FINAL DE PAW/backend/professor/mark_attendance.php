<?php
// backend/professor/mark_attendance.php
require_once __DIR__.'/../functions.php';
require_roles(['professor']);

$input = json_decode(file_get_contents('php://input'), true);
$course_id = (int)($input['course_id'] ?? 0);
$date = $input['date'] ?? date('Y-m-d');
$marks = $input['marks'] ?? []; // array of {student_id, presence (0/1), participation}

if (!$course_id || !is_array($marks)) {
    json_response(['error' => 'bad input']);
}

// LOOP
foreach ($marks as $m) {
    $sid = (int)$m['student_id'];
    $pres = $m['presence'] ? 1 : 0;

    // validate participation
    $part = in_array($m['participation'], ['none','low','medium','high']) 
        ? $m['participation'] 
        : 'none';

    // UPSERT attendance
    $stmt = $pdo->prepare(
        'SELECT id FROM attendance 
         WHERE course_id=? AND student_id=? AND date=? 
         LIMIT 1'
    );
    $stmt->execute([$course_id, $sid, $date]);
    $row = $stmt->fetch();

    if ($row) {
        // UPDATE
        $pdo->prepare(
            'UPDATE attendance SET presence=?, participation=? WHERE id=?'
        )->execute([$pres, $part, $row['id']]);
    } else {
        // INSERT
        $pdo->prepare(
            'INSERT INTO attendance (course_id, student_id, date, presence, participation) 
             VALUES (?, ?, ?, ?, ?)'
        )->execute([$course_id, $sid, $date, $pres, $part]);
    }
}

json_response(['ok' => true]);
