<?php
// api/get_attendance.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';

$sessions = 6; // modifier si besoin

try {
    // Récupérer tous les étudiants
    $stmt = $pdo->query("SELECT id, student_id, last_name, first_name, email FROM students ORDER BY last_name, first_name");
    $students = $stmt->fetchAll();

    // Préparer la structure
    $result = [];
    foreach ($students as $s) {
        $row = $s;
        $row['attendance'] = [];

        // initialiser par défaut sessions
        for ($i = 1; $i <= $sessions; $i++) {
            $row['attendance'][$i] = ['presence' => 0, 'participation' => 0];
        }
        $result[$s['id']] = $row;
    }

    if (count($students) > 0) {
        // récupérer les lignes d'attendance existantes
        $in = implode(',', array_keys($result) ?: [0]);
        $stmt2 = $pdo->prepare("SELECT student_id, session_index, presence, participation FROM attendance WHERE student_id IN ($in)");
        $stmt2->execute();
        $rows = $stmt2->fetchAll();

        foreach ($rows as $r) {
            $sid = $r['student_id'];
            $idx = (int)$r['session_index'];
            if (isset($result[$sid])) {
                $result[$sid]['attendance'][$idx] = [
                    'presence' => (int)$r['presence'],
                    'participation' => (int)$r['participation']
                ];
            }
        }
    }

    // reindex numeriquement
    $out = array_values($result);
    echo json_encode(['sessions' => $sessions, 'students' => $out]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'failed', 'message' => $e->getMessage()]);
}
