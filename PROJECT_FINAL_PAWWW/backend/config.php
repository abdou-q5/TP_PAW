<?php
$host = "localhost";
$dbname = "attendance_db"; // اسم قاعدة البيانات
$user = "root";             // افتراضي WAMP/XAMPP
$pass = "";                 // افتراضي WAMP/XAMPP

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>

