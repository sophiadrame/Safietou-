<?php
require_once "connexion.php";
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_client'])) {
    header("Location: login.php");
    exit();
}

// Vérifier si l'ID de la réservation est fourni
if (!isset($_GET['id'])) {
    header("Location: reservations.php");
    exit();
}

$id_reservation = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_reservation) {
    header("Location: reservations.php");
    exit();
}

// Récupérer les informations de la réservation
$stmt = $pdo->prepare("
    SELECT r.*, v.nom as nom_voiture, v.immatriculation, c.nom as nom_client, c.prenom as prenom_client
    FROM reservation r
    JOIN voiture v ON r.id_voiture = v.id_voiture
    JOIN client c ON r.id_client = c.id_client
    WHERE r.id_reservation = ? AND r.id_client = ? AND r.statut = 'en attente'
");
$stmt->execute([$id_reservation, $_SESSION['id_client']]);
$reservation = $stmt->fetch();

if (!$reservation) {
    header("Location: reservations.php?error=1");
    exit();
}

// Traitement du formulaire de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_carte = filter_input(INPUT_POST, 'numero_carte', FILTER_SANITIZE_STRING);
    $date_expiration = filter_input(INPUT_POST, 'date_expiration', FILTER_SANITIZE_STRING);
    $cvv = filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_STRING);
    
    // Validation des données
    if (!$numero_carte || !$date_expiration || !$cvv) {
        $error = "Veuillez remplir tous les champs.";
    } elseif (strlen($numero_carte) !== 16 || !is_numeric($numero_carte)) {
        $error = "Le numéro de carte doit contenir 16 chiffres.";
    } elseif (strlen($cvv) !== 3 || !is_numeric($cvv)) {
        $error = "Le code CVV doit contenir 3 chiffres.";
    } else {
        // Simuler le traitement du paiement
        // Dans un environnement de production, vous utiliseriez une passerelle de paiement réelle
        
        // Mettre à jour le statut de la réservation
        $stmt = $pdo->prepare("UPDATE reservation SET statut = 'confirmée' WHERE id_reservation = ?");
        
        if ($stmt->execute([$id_reservation])) {
            header("Location: reservations.php?success=2");
            exit();
        } else {
            $error = "Une erreur est survenue lors du traitement du paiement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - Location de Voitures</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Paiement de la réservation</h1>
            <nav>
                <a href="index.php">Accueil</a>
                <a href="reservations.php">Mes Réservations</a>
                <a href="deconnexion.php">Déconnexion</a>
            </nav>
        </header>

        <?php if (isset($error)): ?>
            <div class="alert error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="paiement-form">
            <div class="reservation-summary">
                <h2>Récapitulatif de la réservation</h2>
                <p>Client: <?php echo htmlspecialchars($reservation['prenom_client'] . ' ' . $reservation['nom_client']); ?></p>
                <p>Voiture: <?php echo htmlspecialchars($reservation['nom_voiture']); ?></p>
                <p>Immatriculation: <?php echo htmlspecialchars($reservation['immatriculation']); ?></p>
                <p>Date de début: <?php echo date('d/m/Y', strtotime($reservation['date_debut'])); ?></p>
                <p>Date de fin: <?php echo date('d/m/Y', strtotime($reservation['date_fin'])); ?></p>
                <p class="total">Montant total: <?php echo number_format($reservation['montant_total'], 2); ?> €</p>
            </div>

            <form method="POST" class="form">
                <div class="form-group">
                    <label for="numero_carte">Numéro de carte:</label>
                    <input type="text" id="numero_carte" name="numero_carte" required 
                           maxlength="16" pattern="[0-9]{16}" placeholder="1234 5678 9012 3456">
                </div>

                <div class="form-group">
                    <label for="date_expiration">Date d'expiration:</label>
                    <input type="text" id="date_expiration" name="date_expiration" required 
                           maxlength="5" pattern="[0-9]{2}/[0-9]{2}" placeholder="MM/AA">
                </div>

                <div class="form-group">
                    <label for="cvv">Code CVV:</label>
                    <input type="text" id="cvv" name="cvv" required 
                           maxlength="3" pattern="[0-9]{3}" placeholder="123">
                </div>

                <button type="submit" class="btn-primary">Effectuer le paiement</button>
            </form>
        </div>
    </div>

    <script>
        // Formatage du numéro de carte
        document.getElementById('numero_carte').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = value;
        });

        // Formatage de la date d'expiration
        document.getElementById('date_expiration').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });

        // Formatage du CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    </script>
</body>
</html> 