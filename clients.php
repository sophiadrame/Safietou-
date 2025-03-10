<?php
require_once "config.php";

class Client {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function inscription($nom, $prenom, $email, $mot_de_passe, $telephone) {
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO clients (nom, prenom, email, mot_de_passe, telephone) 
                VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => $mot_de_passe_hash,
            'telephone' => $telephone
        ]);
    }

    public function connexion($email, $mot_de_passe) {
        $sql = "SELECT * FROM clients WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $client = $stmt->fetch();

        if ($client && password_verify($mot_de_passe, $client['mot_de_passe'])) {
            return $client;
        }
        return false;
    }

    public function getReservations($id_client) {
        $sql = "SELECT r.*, v.marque, v.modele, v.plaque_immatriculation 
                FROM reservations r 
                JOIN voitures v ON r.id_voiture = v.id_voiture 
                WHERE r.id_client = :id_client 
                ORDER BY r.date_creation DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_client' => $id_client]);
        return $stmt->fetchAll();
    }
} 