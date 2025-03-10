# Système de Location de Voitures

Ce projet est un système de gestion de location de voitures développé en PHP avec une base de données MySQL.

## Structure du Projet

```
projet_php/
├── admin.php              # Tableau de bord administrateur
├── clients.php           # Gestion des clients
├── connexion.php         # Configuration de la base de données
├── deconnexion.php       # Déconnexion
├── images_voitures/      # Dossier contenant les images des voitures
├── index.php            # Page d'accueil
├── login.php            # Page de connexion
├── paiement.php         # Gestion des paiements
├── rapport_reservation.php # Rapports de réservation
├── register.php         # Inscription des clients
├── reservations.php     # Gestion des réservations
├── styles.css           # Styles CSS
└── voiture.php          # Gestion des voitures
```

## Installation

1. Créer une base de données MySQL nommée `location_voiture`
2. Importer le fichier SQL fourni
3. Configurer les paramètres de connexion dans `connexion.php`
4. Créer le dossier `images_voitures` avec les permissions appropriées
5. Ajouter une image `default.jpg` dans le dossier `images_voitures`

## Base de Données

### Tables

1. `admin`
   - id_admin (INT, PK)
   - nom (VARCHAR)
   - email (VARCHAR)
   - password (VARCHAR)

2. `client`
   - id_client (INT, PK)
   - nom (VARCHAR)
   - prenom (VARCHAR)
   - email (VARCHAR)
   - password (VARCHAR)
   - actif (TINYINT)

3. `voiture`
   - id_voiture (INT, PK)
   - nom (VARCHAR)
   - immatriculation (VARCHAR)
   - prix_journalier (DECIMAL)
   - description (TEXT)
   - disponible (TINYINT)

4. `reservation`
   - id_reservation (INT, PK)
   - id_client (INT, FK)
   - id_voiture (INT, FK)
   - date_debut (DATE)
   - date_fin (DATE)
   - montant_total (DECIMAL)
   - statut (VARCHAR)

## Utilisation

### Client
1. S'inscrire via `register.php`
2. Se connecter via `login.php`
3. Consulter les voitures disponibles sur la page d'accueil
4. Réserver une voiture
5. Effectuer le paiement
6. Gérer ses réservations

### Administrateur
1. Se connecter via `login.php`
2. Accéder au tableau de bord via `admin.php`
3. Gérer les voitures via `voiture.php`
4. Gérer les clients via `clients.php`
5. Consulter les rapports via `rapport_reservation.php`

## Sécurité

- Protection contre les injections SQL avec PDO
- Hachage des mots de passe
- Validation des données
- Protection contre les attaques XSS
- Gestion des sessions

## Fonctionnalités

- Inscription et connexion des clients
- Gestion des voitures (ajout, modification, suppression)
- Système de réservation
- Paiement sécurisé
- Gestion des statuts de réservation
- Rapports et statistiques
- Interface responsive 