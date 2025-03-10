<?php
require_once "connexion.php";
session_start();

// Vérification de la connexion client
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'client') {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

// Récupération des réservations du client
$sql = "SELECT r.*, v.nom as voiture_nom, v.immatriculation 
        FROM reservation r 
        JOIN voiture v ON r.id_voiture = v.id_voiture 
        WHERE r.id_client = :id_client 
        ORDER BY r.date_debut DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["id_client" => $_SESSION['user']['id_client']]);
$reservations = $stmt->fetchAll();

// Traitement de l'annulation de réservation
if (isset($_POST['annuler']) && isset($_POST['id_reservation'])) {
    $id_reservation = filter_input(INPUT_POST, 'id_reservation', FILTER_SANITIZE_NUMBER_INT);
    
    if ($id_reservation) {
        $sql = "UPDATE reservation SET statut = 'annulée' 
                WHERE id_reservation = :id_reservation 
                AND id_client = :id_client 
                AND statut = 'en attente'";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            "id_reservation" => $id_reservation,
            "id_client" => $_SESSION['user']['id_client']
        ])) {
            $success = "Réservation annulée avec succès.";
            // Recharger la page pour mettre à jour la liste
            header("Location: reservations.php");
            exit();
        } else {
            $error = "Erreur lors de l'annulation de la réservation.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - Location de Voitures</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mes Réservations</h1>
            <div class="nav">
                <a href="index.php" class="btn">Accueil</a>
                <a href="reservations.php" class="btn active">Mes Réservations</a>
                <a href="deconnexion.php" class="btn btn-danger">Déconnexion</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (empty($reservations)): ?>
            <p class="message">Vous n'avez pas encore de réservations.</p>
        <?php else: ?>
            <div class="reservations-list">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="reservation-card">
                        <h3><?= htmlspecialchars($reservation['voiture_nom']) ?></h3>
                        <p>Immatriculation : <?= htmlspecialchars($reservation['immatriculation']) ?></p>
                        <p>Date de début : <?= date('d/m/Y', strtotime($reservation['date_debut'])) ?></p>
                        <p>Date de fin : <?= date('d/m/Y', strtotime($reservation['date_fin'])) ?></p>
                        <p>Montant total : <?= number_format($reservation['montant_total'], 2) ?> €</p>
                        <p>Statut : 
                            <span class="status <?= $reservation['statut'] ?>">
                                <?= htmlspecialchars($reservation['statut']) ?>
                            </span>
                        </p>
                        
                        <?php if ($reservation['statut'] == 'en attente'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id_reservation" value="<?= $reservation['id_reservation'] ?>">
                                <button type="submit" name="annuler" class="btn btn-danger">Annuler la réservation</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 