-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_encadreurs
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE gestion_encadreurs;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'etudiant', 'encadreur') NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des étudiants
CREATE TABLE IF NOT EXISTS etudiants (
  id INT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  classe VARCHAR(50) DEFAULT NULL,
  theme VARCHAR(255) DEFAULT NULL,
  binome TINYINT(1) DEFAULT 0,
  fichier_pdf VARCHAR(255) DEFAULT NULL,
  form_submitted TINYINT(1) DEFAULT 0,
  date_soumission DATETIME DEFAULT NULL,
  date_modification DATETIME DEFAULT NULL,
  role VARCHAR(20) DEFAULT 'etudiant',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table des encadreurs
CREATE TABLE IF NOT EXISTS encadreurs (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100) DEFAULT NULL,
  competences VARCHAR(255) DEFAULT NULL,
  mot_de_passe VARCHAR(255) DEFAULT NULL,
  date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des affectations
CREATE TABLE IF NOT EXISTS affectations (
  id INT PRIMARY KEY AUTO_INCREMENT,
  etudiant_id INT NOT NULL,
  encadreur_id INT NOT NULL,
  date_affectation DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE,
  FOREIGN KEY (encadreur_id) REFERENCES encadreurs(id) ON DELETE CASCADE,
  UNIQUE KEY unique_affectation (etudiant_id)
) ENGINE=InnoDB;

-- Table des relances
CREATE TABLE IF NOT EXISTS relances (
  id INT PRIMARY KEY AUTO_INCREMENT,
  etudiant_id INT NOT NULL,
  date_relance DATETIME DEFAULT CURRENT_TIMESTAMP,
  traite TINYINT(1) DEFAULT 0,
  FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Création de l'utilisateur administrateur
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@example.com', '$2a$12$0G8O8Jz102PfmhHa9Kj4Te4Ih2TsGPeJ0Oy5tE4SmpTATbxusnN3m', 'admin');
-- Mot de passe: admin123

-- Création de quelques étudiants
INSERT INTO users (username, email, password, role) VALUES 
('etudiant1', 'etudiant1@example.com', '$2a$12$ETILnklbs6rYJclQrAFs0u5Y6Eq/K6XIRUHhGQoFuKyoKc/0pf/8O', 'etudiant'),
('etudiant2', 'etudiant2@example.com', '$2a$12$ETILnklbs6rYJclQrAFs0u5Y6Eq/K6XIRUHhGQoFuKyoKc/0pf/8O', 'etudiant'),
('etudiant3', 'etudiant3@example.com', '$2a$12$ETILnklbs6rYJclQrAFs0u5Y6Eq/K6XIRUHhGQoFuKyoKc/0pf/8O', 'etudiant');
-- Mot de passe: etudiant123

-- Création des profils étudiants correspondants
INSERT INTO etudiants (id, nom, prenom, email, classe) VALUES 
(2, 'Dupont', 'Jean', 'etudiant1@example.com', 'MIAGE L3'),
(3, 'Martin', 'Sophie', 'etudiant2@example.com', 'INFO L3'),
(4, 'Dubois', 'Pierre', 'etudiant3@example.com', 'RESEAUX L3');

-- Création de quelques encadreurs
INSERT INTO encadreurs (nom, prenom, email, competences) VALUES 
('Leclerc', 'Marie', 'encadreur1@example.com', 'AL,SI'),
('Moreau', 'Michel', 'encadreur2@example.com', 'SRC,DATA,WEB'),
('Petit', 'Claire', 'encadreur3@example.com', 'MOBILE,IOT,IA');

-- Création d'un utilisateur pour le premier encadreur
INSERT INTO users (username, email, password, role) VALUES 
('encadreur1', 'encadreur1@example.com', '$2a$12$Bz2i.LBIDX.W85SQu0phDOSEiFH/azYym1MupmRzVv9wZxfxhedP.', 'encadreur');
-- Mot de passe: encadreur123

-- Affectation d'un étudiant à un encadreur
INSERT INTO affectations (etudiant_id, encadreur_id) VALUES 
(2, 1);  -- Jean Dupont est affecté à Marie Leclerc