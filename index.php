<?php
require_once "connexion.php";
session_start();

// Récupération des voitures disponibles
$sql = "SELECT * FROM voiture WHERE disponible = 1 ORDER BY nom";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$voitures = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location de Voitures</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Location de Voitures</h1>
            <div class="nav">
                <a href="index.php" class="btn active">Accueil</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($_SESSION['user_type'] == 'admin'): ?>
                        <a href="admin.php" class="btn">Administration</a>
                    <?php else: ?>
                        <a href="reservations.php" class="btn">Mes Réservations</a>
                    <?php endif; ?>
                    <a href="deconnexion.php" class="btn btn-danger">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="btn">Connexion</a>
                    <a href="register.php" class="btn">Inscription</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="hero">
            <h2>Bienvenue sur notre site de location de voitures</h2>
            <p>Découvrez notre sélection de voitures disponibles pour vos déplacements.</p>
        </div>

        <!-- Liste des voitures disponibles -->
        <div class="available-cars">
            <h2>Nos Voitures Disponibles</h2>
            <?php if (empty($voitures)): ?>
                <p class="no-data">Aucune voiture disponible pour le moment.</p>
            <?php else: ?>
                <div class="cars-grid">
                    <?php foreach ($voitures as $voiture): ?>
                        <div class="car-card">
                            <div class="car-image">
                                <?php
                                $image_path = "images_voitures/" . $voiture['id_voiture'] . ".jpg";
                                if (file_exists($image_path)): ?>
                                    <img src="<?= htmlspecialchars($image_path) ?>" 
                                         alt="<?= htmlspecialchars($voiture['nom']) ?>">
                                <?php else: ?>
                                    <img src="images_voitures/default.jpg" alt="Image par défaut">
                                <?php endif; ?>
                            </div>
                            <div class="car-info">
                                <h3><?= htmlspecialchars($voiture['nom']) ?></h3>
                                <p>Prix par jour : <?= number_format($voiture['prix_journalier'], 2) ?> €</p>
                                <?php if (!empty($voiture['description'])): ?>
                                    <p class="description"><?= htmlspecialchars($voiture['description']) ?></p>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['user']) && $_SESSION['user_type'] == 'client'): ?>
                                    <a href="reserver.php?id=<?= $voiture['id_voiture'] ?>" class="btn">Réserver</a>
                                <?php else: ?>
                                    <a href="login.php" class="btn">Connectez-vous pour réserver</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="features">
            <div class="feature">
                <h3>Service de qualité</h3>
                <p>Des voitures en parfait état et un service client réactif.</p>
            </div>
            <div class="feature">
                <h3>Tarifs compétitifs</h3>
                <p>Des prix avantageux pour tous types de locations.</p>
            </div>
            <div class="feature">
                <h3>Réservation simple</h3>
                <p>Un processus de réservation rapide et efficace.</p>
            </div>
        </div>
    </div>
</body>
</html> 