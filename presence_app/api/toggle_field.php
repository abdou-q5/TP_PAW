<?php
// api/toggle_field.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$sid = $input['studentIdDb'] ?? null;
$sessionIndex = isset($input['sessionIndex']) ? (int)$input['sessionIndex'] : null;
$field = $input['field'] ?? null;

if (!$sid || !$sessionIndex || !in_array($field, ['presence','participation'])) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_request']);
    exit;
}

try {
    // vérifier si ligne existe
    $stmt = $pdo->prepare("SELECT id, presence, participation FROM attendance WHERE student_id = ? AND session_index = ?");
    $stmt->execute([$sid, $sessionIndex]);
    $row = $stmt->fetch();

    if ($row) {
        // toggle
        $newVal = $row[$field] ? 0 : 1;
        $upd = $pdo->prepare("UPDATE attendance SET $field = ? WHERE id = ?");
        $upd->execute([$newVal, $row['id']]);
    } else {
        // créer ligne et définir le champ voulu à 1
        $presence = ($field === 'presence') ? 1 : 0;
        $participation = ($field === 'participation') ? 1 : 0;
        $ins = $pdo->prepare("INSERT INTO attendance (student_id, session_index, presence, participation) VALUES (?, ?, ?, ?)");
        $ins->execute([$sid, $sessionIndex, $presence, $participation]);
        $newVal = ($field === 'presence' ? $presence : $participation);
    }

    echo json_encode(['success' => true, 'field' => $field, 'value' => (int)$newVal]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'db_error', 'message' => $e->getMessage()]);
}
