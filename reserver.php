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

// Récupération de l'ID de la voiture si fourni
$id_voiture = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_client = $_SESSION['user']['id_client'];
    $id_voiture = filter_input(INPUT_POST, 'id_voiture', FILTER_SANITIZE_NUMBER_INT);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Validation des dates
    if (strtotime($date_debut) > strtotime($date_fin)) {
        $error = "La date de début doit être antérieure à la date de fin.";
    } else {
        // Vérification de la disponibilité
        $sql = "SELECT COUNT(*) FROM reservation 
                WHERE id_voiture = :id_voiture 
                AND statut = 'confirmée' 
                AND (date_debut <= :date_fin AND date_fin >= :date_debut)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            "id_voiture" => $id_voiture,
            "date_debut" => $date_debut,
            "date_fin" => $date_fin
        ]);
        $disponible = $stmt->fetchColumn() == 0;

        if ($disponible) {
            // Calcul du montant total
            $sql = "SELECT prix_journalier FROM voiture WHERE id_voiture = :id_voiture";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["id_voiture" => $id_voiture]);
            $voiture = $stmt->fetch();

            $jours = (strtotime($date_fin) - strtotime($date_debut)) / (60 * 60 * 24);
            $montant_total = $voiture['prix_journalier'] * $jours;

            // Insertion de la réservation
            $sql = "INSERT INTO reservation (id_client, id_voiture, date_debut, date_fin, montant_total, statut) 
                    VALUES (:id_client, :id_voiture, :date_debut, :date_fin, :montant_total, 'en attente')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                "id_client" => $id_client,
                "id_voiture" => $id_voiture,
                "date_debut" => $date_debut,
                "date_fin" => $date_fin,
                "montant_total" => $montant_total
            ]);

            $id_reservation = $pdo->lastInsertId();
            header("Location: paiement.php?id=" . $id_reservation);
            exit();
        } else {
            $error = "Cette voiture n'est pas disponible pour cette période.";
        }
    }
}

// Récupération des informations de la voiture si un ID est fourni
$voiture = null;
if ($id_voiture) {
    $sql = "SELECT * FROM voiture WHERE id_voiture = :id_voiture AND disponible = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["id_voiture" => $id_voiture]);
    $voiture = $stmt->fetch();
}

// Récupération des voitures disponibles
$sql = "SELECT * FROM voiture WHERE disponible = 1 ORDER BY nom";
$stmt = $pdo->query($sql);
$voitures = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver une voiture</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Réserver une voiture</h1>
            <div class="nav">
                <a href="index.php" class="btn">Accueil</a>
                <a href="reservations.php" class="btn">Mes Réservations</a>
                <a href="deconnexion.php" class="btn btn-danger">Déconnexion</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="form">
            <form method="POST">
                <?php if ($voiture): ?>
                    <input type="hidden" name="id_voiture" value="<?= $voiture['id_voiture'] ?>">
                    <div class="form-group">
                        <label>Voiture sélectionnée :</label>
                        <p><?= htmlspecialchars($voiture['nom']) ?> - <?= htmlspecialchars($voiture['immatriculation']) ?></p>
                        <p>Prix journalier : <?= number_format($voiture['prix_journalier'], 2) ?> €</p>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="date_debut">Date de début :</label>
                    <input type="date" id="date_debut" name="date_debut" required 
                           min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="date_fin">Date de fin :</label>
                    <input type="date" id="date_fin" name="date_fin" required 
                           min="<?= date('Y-m-d') ?>">
                </div>

                <?php if (!$voiture): ?>
                    <div class="form-group">
                        <label for="id_voiture">Sélectionner une voiture :</label>
                        <select id="id_voiture" name="id_voiture" required>
                            <option value="">Choisir une voiture</option>
                            <?php foreach ($voitures as $v): ?>
                                <option value="<?= $v['id_voiture'] ?>">
                                    <?= htmlspecialchars($v['nom']) ?> - 
                                    <?= htmlspecialchars($v['immatriculation']) ?> - 
                                    <?= number_format($v['prix_journalier'], 2) ?> €/jour
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn">Réserver</button>
            </form>
        </div>
    </div>

    <script>
        // Validation des dates
        document.getElementById('date_fin').addEventListener('change', function() {
            const dateDebut = document.getElementById('date_debut').value;
            const dateFin = this.value;
            if (dateDebut && dateFin && dateDebut > dateFin) {
                alert("La date de fin doit être postérieure à la date de début.");
                this.value = '';
            }
        });
    </script>
</body>
</html>