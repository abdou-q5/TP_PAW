<?php
require_once __DIR__ . '/db_connect.php';
$pdo = db_connect();
if (!$pdo) die("DB connection failed.");

$rows = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
echo "<h2>Students</h2><ul>";
foreach ($rows as $r) {
    echo "<li>ID: {$r['id']} — {$r['fullname']} — {$r['matricule']} — {$r['group_id']} 
        [<a href='delete_student.php?id={$r['id']}'>delete</a>]</li>";
}
echo "</ul>";
