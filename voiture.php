<?php
require_once "connexion.php";
session_start();

// Vérification de la connexion admin
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

// Traitement de l'ajout d'une voiture
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter'])) {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $immatriculation = filter_input(INPUT_POST, 'immatriculation', FILTER_SANITIZE_STRING);
    $prix_journalier = filter_input(INPUT_POST, 'prix_journalier', FILTER_VALIDATE_FLOAT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if ($nom && $immatriculation && $prix_journalier && $description) {
        $sql = "INSERT INTO voiture (nom, immatriculation, prix_journalier, description, disponible) 
                VALUES (:nom, :immatriculation, :prix_journalier, :description, 1)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            "nom" => $nom,
            "immatriculation" => $immatriculation,
            "prix_journalier" => $prix_journalier,
            "description" => $description
        ])) {
            $success = "Voiture ajoutée avec succès.";
        } else {
            $error = "Erreur lors de l'ajout de la voiture.";
        }
    } else {
        $error = "Veuillez remplir tous les champs correctement.";
    }
}

// Traitement de la modification du statut
if (isset($_POST['modifier_statut'])) {
    $id_voiture = filter_input(INPUT_POST, 'id_voiture', FILTER_SANITIZE_NUMBER_INT);
    $disponible = filter_input(INPUT_POST, 'disponible', FILTER_VALIDATE_BOOLEAN);

    if ($id_voiture !== false && $disponible !== false) {
        $sql = "UPDATE voiture SET disponible = :disponible WHERE id_voiture = :id_voiture";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            "id_voiture" => $id_voiture,
            "disponible" => $disponible
        ])) {
            $success = "Statut de la voiture mis à jour avec succès.";
        } else {
            $error = "Erreur lors de la mise à jour du statut.";
        }
    }
}

// Récupération des voitures
$sql = "SELECT * FROM voiture ORDER BY nom";
$stmt = $pdo->query($sql);
$voitures = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Voitures - Administration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestion des Voitures</h1>
            <div class="nav">
                <a href="admin.php" class="btn">Tableau de bord</a>
                <a href="voiture.php" class="btn active">Voitures</a>
                <a href="clients.php" class="btn">Clients</a>
                <a href="rapport_reservation.php" class="btn">Rapports</a>
                <a href="deconnexion.php" class="btn btn-danger">Déconnexion</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Formulaire d'ajout de voiture -->
        <div class="form-section">
            <h2>Ajouter une nouvelle voiture</h2>
            <form method="POST" class="form">
                <div class="form-group">
                    <label for="nom">Nom de la voiture :</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="immatriculation">Immatriculation :</label>
                    <input type="text" id="immatriculation" name="immatriculation" required>
                </div>

                <div class="form-group">
                    <label for="prix_journalier">Prix journalier (€) :</label>
                    <input type="number" id="prix_journalier" name="prix_journalier" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="description">Description :</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <button type="submit" name="ajouter" class="btn">Ajouter la voiture</button>
            </form>
        </div>

        <!-- Liste des voitures -->
        <div class="list-section">
            <h2>Liste des voitures</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Immatriculation</th>
                        <th>Prix journalier</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($voitures as $voiture): ?>
                        <tr>
                            <td><?= htmlspecialchars($voiture['nom']) ?></td>
                            <td><?= htmlspecialchars($voiture['immatriculation']) ?></td>
                            <td><?= number_format($voiture['prix_journalier'], 2) ?> €</td>
                            <td><?= htmlspecialchars($voiture['description']) ?></td>
                            <td>
                                <span class="status <?= $voiture['disponible'] ? 'disponible' : 'indisponible' ?>">
                                    <?= $voiture['disponible'] ? 'Disponible' : 'Indisponible' ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_voiture" value="<?= $voiture['id_voiture'] ?>">
                                    <input type="hidden" name="disponible" value="<?= $voiture['disponible'] ? '0' : '1' ?>">
                                    <button type="submit" name="modifier_statut" class="btn">
                                        <?= $voiture['disponible'] ? 'Rendre indisponible' : 'Rendre disponible' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 