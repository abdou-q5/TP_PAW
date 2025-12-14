<?php
require_once '../app/includes/auth.php';
requireRole('student');
require_once '../app/config/db.php';

header('Content-Type: application/json');
$db = getConnection();

$studentId = $_SESSION['user_id'];
$sessionId = (int)($_POST['session_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');

if (!$sessionId || $reason === '' || !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

$stmt = $db->prepare("
  SELECT id FROM attendance_records
  WHERE session_id = :sid AND student_id = :stid
");
$stmt->execute([':sid' => $sessionId, ':stid' => $studentId]);
$rec = $stmt->fetch();

if (!$rec) {
    $ins = $db->prepare("
      INSERT INTO attendance_records (session_id, student_id, status, updated_at)
      VALUES (:sid, :stid, 'absent', NOW())
    ");
    $ins->execute([':sid' => $sessionId, ':stid' => $studentId]);
    $recordId = $db->lastInsertId();
} else {
    $recordId = $rec['id'];
}

$uploadDir = __DIR__ . '/uploads/justifications/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

$allowed = ['application/pdf', 'image/jpeg', 'image/png'];
if (!in_array($_FILES['file']['type'], $allowed)) {
    echo json_encode(['success' => false, 'error' => 'Type de fichier invalide']);
    exit;
}

$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
$filename = 'justif_' . $recordId . '_' . time() . '.' . $ext;
$dest = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
    echo json_encode(['success' => false, 'error' => 'Upload échoué']);
    exit;
}

$relPath = 'uploads/justifications/' . $filename;

$ins2 = $db->prepare("
  INSERT INTO justification_requests 
    (attendance_record_id, student_id, reason, file_path, status, submitted_at)
  VALUES (:arid, :sid, :r, :fp, 'pending', NOW())
");
$ins2->execute([
  ':arid' => $recordId,
  ':sid'  => $studentId,
  ':r'    => $reason,
  ':fp'   => $relPath
]);

echo json_encode(['success' => true]);