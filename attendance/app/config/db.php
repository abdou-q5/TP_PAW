<?php
function getConnection() {
    $host = 'localhost';
    $db   = 'attendance_db';
    $user = 'root';   // adapte selon XAMPP/WAMP
    $pass = '';       // mot de passe si existant
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        error_log("DB Connection error: " . $e->getMessage());
        die("Database connection error.");
    }
}