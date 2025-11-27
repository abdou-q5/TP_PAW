<?php
// db/db_connect.php
require_once __DIR__ . '/config.php';

function db_connect() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    try {
        $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8", $DB_USER, $DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        file_put_contents(__DIR__ . '/db_errors.log', date('c') . ' ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        return null;
    }
}
