<?php
require_once __DIR__ . '/db_connect.php';
$pdo = db_connect();
if ($pdo) echo "Connection successful.";
else echo "Connection failed. Check config and MySQL server.";
