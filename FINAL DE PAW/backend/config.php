<?php
// backend/config.php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'attendance_system';
$DB_USER = 'root';
$DB_PASS = ''; // mettre mot de passe si nécessaire


date_default_timezone_set('Africa/Algiers');


try {
$pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
} catch (Exception $e) {
http_response_code(500);
echo json_encode(['error' => 'DB connection failed', 'msg' => $e->getMessage()]);
exit;
}
session_start();