<?php
require_once '../app/config/db.php';

try {
    $db = getConnection();
    echo "Connexion OK<br>";
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "Tables trouvées :<br>";
    foreach ($tables as $t) {
        echo htmlspecialchars(implode(', ', $t)) . "<br>";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}