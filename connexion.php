<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?> 