-- Création de la base de données
-- CREATE DATABASE IF NOT EXISTS location_voiture;
USE sophiaiam_location_voiture;

-- Table admin
CREATE TABLE IF NOT EXISTS admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Table client
CREATE TABLE IF NOT EXISTS client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    actif TINYINT(1) DEFAULT 1
);

-- Table voiture
CREATE TABLE IF NOT EXISTS voiture (
    id_voiture INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    immatriculation VARCHAR(20) NOT NULL UNIQUE,
    prix_journalier DECIMAL(10,2) NOT NULL,
    description TEXT,
    disponible TINYINT(1) DEFAULT 1
);

-- Table reservation
CREATE TABLE IF NOT EXISTS reservation (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_voiture INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    statut ENUM('en attente', 'confirmée', 'annulée') DEFAULT 'en attente',
    FOREIGN KEY (id_client) REFERENCES client(id_client),
    FOREIGN KEY (id_voiture) REFERENCES voiture(id_voiture)
);

-- Insertion d'un administrateur par défaut (mot de passe : admin123)
INSERT INTO admin (nom, email, password) VALUES 
('Administrateur', 'admin@admin.com', '$2y$10$8zg7.pD0h7VNGAz98J0R8.dG5f.WQpJ8ma6R.pOZymsv4hY8U.NTi');

-- Insertion de quelques voitures de démonstration
INSERT INTO voiture (nom, immatriculation, prix_journalier, description, disponible) VALUES
('Renault Clio', 'AA-123-BB', 50.00, 'Citadine confortable et économique', 1),
('Peugeot 308', 'BB-456-CC', 65.00, 'Compacte familiale avec grand coffre', 1),
('Volkswagen Golf', 'CC-789-DD', 70.00, 'Berline polyvalente et fiable', 1),
('Toyota Yaris', 'DD-012-EE', 45.00, 'Petite citadine hybride', 1),
('Ford Focus', 'EE-345-FF', 60.00, 'Berline familiale spacieuse', 1); 