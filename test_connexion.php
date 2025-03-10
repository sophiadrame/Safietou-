<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    echo "Connexion réussie à la base de données !";
    
    // Test d'une requête simple
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin");
    $result = $stmt->fetch();
    echo "<br>Nombre d'administrateurs : " . $result['total'];
    
} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
} 