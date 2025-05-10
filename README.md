# Gestion d'Encadreurs

Une application PHP pour gérer l'affectation d'encadreurs aux étudiants dans un contexte académique. Cette application permet aux administrateurs de gérer les encadreurs, aux étudiants de soumettre leurs cahiers des charges, et facilite le processus d'affectation.

## Table des matières

- [Présentation](#présentation)
- [Fonctionnalités](#fonctionnalités)
- [Installation](#installation)
- [Structure du projet](#structure-du-projet)
- [Rôles utilisateurs](#rôles-utilisateurs)
- [Modifications majeures](#modifications-majeures)
- [Utilisation](#utilisation)
- [Base de données](#base-de-données)
- [Améliorations futures possibles](#améliorations-futures-possibles)

## Présentation

Cette application de gestion d'encadreurs est conçue pour faciliter l'affectation d'encadreurs aux étudiants dans un contexte académique. Elle offre une interface simple et intuitive permettant aux administrateurs de gérer les encadreurs, aux étudiants de soumettre leurs thèmes de projet et aux encadreurs de suivre leurs étudiants assignés.

## Fonctionnalités

### Pour les administrateurs
- Gestion des encadreurs (ajout, modification, suppression)
- Affectation manuelle d'étudiants aux encadreurs
- Visualisation des étudiants assignés et non assignés
- Statistiques sur les affectations

### Pour les étudiants
- Soumission du cahier des charges (thème, binôme, fichier PDF)
- Visualisation de l'encadreur assigné
- Modification du profil personnel
- Relance pour l'attribution d'un encadreur

### Pour les encadreurs
- Visualisation des étudiants assignés
- Consultation des cahiers des charges soumis

## Installation

1. Clonez ce dépôt sur votre serveur local ou distant
```bash
git clone https://github.com/votre-username/gestion_encadreur.git
```

2. Importez la base de données en utilisant le fichier SQL fourni
```
gestion_encadreurs.sql
```

3. Configurez la connexion à la base de données dans `config/database.php`
```php
// Modifiez ces paramètres selon votre configuration
private static $host = 'localhost';
private static $db_name = 'gestion_encadreurs';
private static $username = 'root';
private static $password = '';
```

4. Assurez-vous que les dossiers suivants ont les droits d'écriture
```bash
chmod 777 public/uploads
```

5. Accédez à l'application via votre navigateur
```
http://localhost/gestion_encadreur
```

## Structure du projet

L'application suit une architecture MVC (Modèle-Vue-Contrôleur) simplifiée :

```
gestion_encadreur/
├── config/
│   └── database.php
├── controllers/
│   ├── AdminController.php
│   ├── AuthController.php
│   ├── EtudiantController.php
│   └── EncadreurController.php
├── middlewares/
│   └── AuthMiddleware.php
├── models/
│   ├── Affectation.php
│   ├── Encadreur.php
│   └── Etudiants.php
├── public/
│   ├── assets/
│   └── uploads/
├── views/
│   ├── admin/
│   ├── auth/
│   ├── etudiant/
│   └── encadreur/
└── index.php
```

## Rôles utilisateurs

L'application gère trois types d'utilisateurs :

1. **Administrateur** : Gère les encadreurs et les affectations
2. **Étudiant** : Soumet son cahier des charges et consulte son encadreur
3. **Encadreur** : Consulte la liste des étudiants qui lui sont assignés

## Modifications majeures

Plusieurs améliorations ont été apportées à l'application initiale :

### 1. Architecture et organisation
- Refactorisation complète suivant le modèle MVC
- Séparation claire des responsabilités entre modèles, vues et contrôleurs
- Meilleure organisation des fichiers et dossiers

### 2. Sécurité
- Implémentation d'un système de middleware pour la gestion des autorisations
- Protection contre les injections SQL avec des requêtes préparées
- Filtrage et validation des entrées utilisateur
- Gestion sécurisée des fichiers uploadés

### 3. Interface utilisateur
- Interface modernisée avec Bootstrap 5 et Font Awesome
- Tableaux de bord pour chaque type d'utilisateur
- Messages flash pour les notifications
- Formulaires plus conviviaux et intuitifs

### 4. Fonctionnalités
- Système d'affectation amélioré pour les administrateurs
- Possibilité de relance pour les étudiants sans encadreur
- Statistiques pour les administrateurs
- Meilleure gestion des profils utilisateurs

## Utilisation

### Connexion administrateur
- Email : admin@example.com
- Mot de passe : admin123

### Connexion étudiant
- Email : etudiant@example.com
- Mot de passe : etudiant123

### Connexion encadreur
- Email : encadreur@example.com
- Mot de passe : encadreur123

## Base de données

Le schéma de base de données comprend les tables suivantes :

- **users** : Informations d'authentification des utilisateurs
- **etudiants** : Profils des étudiants et leurs soumissions
- **encadreurs** : Profils des encadreurs et leurs compétences
- **affectations** : Liens entre étudiants et encadreurs
- **relances** : Historique des relances effectuées par les étudiants

Voici la structure simplifiée :

```sql
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'etudiant', 'encadreur') NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE etudiants (
  id INT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  classe VARCHAR(50),
  theme VARCHAR(255),
  binome TINYINT(1) DEFAULT 0,
  fichier_pdf VARCHAR(255),
  form_submitted TINYINT(1) DEFAULT 0,
  date_soumission DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id) REFERENCES users(id)
);

CREATE TABLE encadreurs (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100),
  competences VARCHAR(255),
  mot_de_passe VARCHAR(255),
  date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE affectations (
  id INT PRIMARY KEY AUTO_INCREMENT,
  etudiant_id INT NOT NULL,
  encadreur_id INT NOT NULL,
  date_affectation DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (etudiant_id) REFERENCES etudiants(id),
  FOREIGN KEY (encadreur_id) REFERENCES encadreurs(id),
  UNIQUE KEY unique_affectation (etudiant_id)
);

CREATE TABLE relances (
  id INT PRIMARY KEY AUTO_INCREMENT,
  etudiant_id INT NOT NULL,
  date_relance DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (etudiant_id) REFERENCES etudiants(id)
);
```

## Améliorations futures possibles

- Implémentation d'un système de messagerie interne entre étudiants et encadreurs
- Ajout de notifications par email
- Interface d'administration plus complète avec des filtres avancés
- Exportation des données au format CSV/PDF
- Système de suivi des projets avec jalons et évaluations
- Interface responsive pour une meilleure expérience sur mobile
