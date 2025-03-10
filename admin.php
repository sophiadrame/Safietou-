<?php
require_once "connexion.php";
session_start();

// Vérification de la connexion admin
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

class Admin {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function connexion($email, $mot_de_passe) {
        $sql = "SELECT * FROM admin WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($mot_de_passe, $admin['password'])) {
            return $admin;
        }
        return false;
    }

    public function getStatistiques() {
        $stats = [];

        // Nombre total de voitures
        $sql = "SELECT COUNT(*) as total FROM voiture";
        $stmt = $this->pdo->query($sql);
        $stats['total_voitures'] = $stmt->fetch()['total'];

        // Nombre de voitures disponibles
        $sql = "SELECT COUNT(*) as disponible FROM voiture WHERE disponible = 1";
        $stmt = $this->pdo->query($sql);
        $stats['voitures_disponibles'] = $stmt->fetch()['disponible'];

        // Nombre total de clients
        $sql = "SELECT COUNT(*) as total FROM client";
        $stmt = $this->pdo->query($sql);
        $stats['total_clients'] = $stmt->fetch()['total'];

        // Nombre de clients actifs
        $sql = "SELECT COUNT(*) as actifs FROM client WHERE actif = 1";
        $stmt = $this->pdo->query($sql);
        $stats['clients_actifs'] = $stmt->fetch()['actifs'];

        // Nombre de réservations en attente
        $sql = "SELECT COUNT(*) as en_attente FROM reservation WHERE statut = 'en attente'";
        $stmt = $this->pdo->query($sql);
        $stats['reservations_en_attente'] = $stmt->fetch()['en_attente'];

        // Nombre de réservations confirmées
        $sql = "SELECT COUNT(*) as confirmees FROM reservation WHERE statut = 'confirmée'";
        $stmt = $this->pdo->query($sql);
        $stats['reservations_confirmees'] = $stmt->fetch()['confirmees'];

        return $stats;
    }

    public function getDernieresReservations() {
        $sql = "SELECT r.*, c.nom, c.prenom, v.nom as voiture_nom
                FROM reservation r
                JOIN client c ON r.id_client = c.id_client
                JOIN voiture v ON r.id_voiture = v.id_voiture
                ORDER BY r.date_debut DESC
                LIMIT 5";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
}

// Création d'une instance de la classe Admin
$admin = new Admin($pdo);

// Récupération des statistiques
$stats = $admin->getStatistiques();

// Récupération des dernières réservations
$dernieres_reservations = $admin->getDernieresReservations();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Administration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tableau de Bord</h1>
            <div class="nav">
                <a href="admin.php" class="btn active">Tableau de bord</a>
                <a href="voiture.php" class="btn">Voitures</a>
                <a href="clients.php" class="btn">Clients</a>
                <a href="rapport_reservation.php" class="btn">Rapports</a>
                <a href="deconnexion.php" class="btn btn-danger">Déconnexion</a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Voitures</h3>
                <p>Total: <?= $stats['total_voitures'] ?></p>
                <p>Disponibles: <?= $stats['voitures_disponibles'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Clients</h3>
                <p>Total: <?= $stats['total_clients'] ?></p>
                <p>Actifs: <?= $stats['clients_actifs'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Réservations</h3>
                <p>En attente: <?= $stats['reservations_en_attente'] ?></p>
                <p>Confirmées: <?= $stats['reservations_confirmees'] ?></p>
            </div>
        </div>

        <!-- Dernières réservations -->
        <div class="table-responsive">
            <h2>Dernières Réservations</h2>
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Voiture</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dernieres_reservations as $reservation): ?>
                        <tr>
                            <td><?= htmlspecialchars($reservation['prenom'] . ' ' . $reservation['nom']) ?></td>
                            <td><?= htmlspecialchars($reservation['voiture_nom']) ?></td>
                            <td><?= date('d/m/Y', strtotime($reservation['date_debut'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($reservation['date_fin'])) ?></td>
                            <td>
                                <span class="status <?= $reservation['statut'] ?>">
                                    <?= htmlspecialchars($reservation['statut']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 