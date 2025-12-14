<?php
require_once '../app/includes/auth.php';
requireRole('professor');
require_once '../app/config/db.php';

header('Content-Type: application/json');

$db = getConnection();
$sessionId = (int)($_POST['session_id'] ?? 0);
$recordsJson = $_POST['records'] ?? '[]';
$records = json_decode($recordsJson, true);

if (!is_array($records)) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

try {
    $db->beginTransaction();
    $stmt = $db->prepare("
        INSERT INTO attendance_records (session_id, student_id, status, participation_score, updated_at)
        VALUES (:sid, :stid, :st, :p, NOW())
        ON DUPLICATE KEY UPDATE 
          status = VALUES(status),
          participation_score = VALUES(participation_score),
          updated_at = NOW()
    ");

    foreach ($records as $r) {
        $stmt->execute([
            ':sid'  => $sessionId,
            ':stid' => (int)$r['student_id'],
            ':st'   => $r['status'],
            ':p'    => ($r['participation'] === '' ? null : (int)$r['participation'])
        ]);
    }
    $db->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $db->rollBack();
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erreur DB']);
}