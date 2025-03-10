<?php
require_once "connexion.php";
session_start();

// Si l'utilisateur est déjà connecté, rediriger vers la page appropriée
if (isset($_SESSION['user'])) {
    if ($_SESSION['user_type'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: reservations.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($email && $password) {
        // Vérification pour l'admin
        $sql = "SELECT * FROM admin WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["email" => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['user'] = [
                'id_admin' => $admin['id_admin'],
                'nom' => $admin['nom'],
                'email' => $admin['email']
            ];
            $_SESSION['user_type'] = 'admin';
            header("Location: admin.php");
            exit();
        } else {
            // Vérification pour le client
            $sql = "SELECT * FROM client WHERE email = :email AND actif = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["email" => $email]);
            $client = $stmt->fetch();

            if ($client && password_verify($password, $client['password'])) {
                $_SESSION['user'] = [
                    'id_client' => $client['id_client'],
                    'nom' => $client['nom'],
                    'prenom' => $client['prenom'],
                    'email' => $client['email']
                ];
                $_SESSION['user_type'] = 'client';
                header("Location: index.php");
                exit();
            } else {
                $error = "Email ou mot de passe incorrect, ou compte désactivé.";
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Location de Voitures</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Connexion</h1>
            <div class="nav">
                <a href="index.php" class="btn">Accueil</a>
                <a href="register.php" class="btn">Inscription</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form">
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html> 