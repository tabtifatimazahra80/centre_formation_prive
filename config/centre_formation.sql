-- =====================================================
-- Centre de formation : base de donnees (MySQL / MariaDB)
-- Conforme au diagramme (role, utilisateur, stagiaire,
-- administrateur, formateur, groupe, module, programmer,
-- seance, cours, evaluation, presence, note)
-- =====================================================

DROP DATABASE IF EXISTS db_centre_formation;
CREATE DATABASE db_centre_formation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_centre_formation;

CREATE TABLE role (
  idRole  INT AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE utilisateur (
  idUtilisateur INT AUTO_INCREMENT PRIMARY KEY,
  nom           VARCHAR(50)  NOT NULL,
  prenom        VARCHAR(50)  NOT NULL,
  email         VARCHAR(100) NOT NULL UNIQUE,
  telephone     VARCHAR(20),
  motDePasse    VARCHAR(255) NOT NULL,
  statut        ENUM('actif','inactif') NOT NULL DEFAULT 'actif',
  idRole        INT NOT NULL,
  CONSTRAINT fk_utilisateur_role FOREIGN KEY (idRole) REFERENCES role(idRole)
) ENGINE=InnoDB;

CREATE TABLE groupe (
  idGroupe   INT AUTO_INCREMENT PRIMARY KEY,
  codeGroupe VARCHAR(50)  NOT NULL UNIQUE,
  nom        VARCHAR(100) NOT NULL,
  filiere    VARCHAR(100) NOT NULL,
  annee      VARCHAR(50)  NOT NULL,
  capacite   INT NOT NULL CHECK (capacite > 0),
  statut     ENUM('actif','inactif') NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB;

CREATE TABLE stagiaire (
  idUtilisateur  INT PRIMARY KEY,
  numInscription VARCHAR(50) NOT NULL UNIQUE,
  dateNaissance  DATE,
  idGroupe       INT NOT NULL,
  CONSTRAINT fk_stagiaire_utilisateur FOREIGN KEY (idUtilisateur) REFERENCES utilisateur(idUtilisateur) ON DELETE CASCADE,
  CONSTRAINT fk_stagiaire_groupe      FOREIGN KEY (idGroupe)      REFERENCES groupe(idGroupe)
) ENGINE=InnoDB;

CREATE TABLE administrateur (
  idUtilisateur INT PRIMARY KEY,
  CONSTRAINT fk_admin_utilisateur FOREIGN KEY (idUtilisateur) REFERENCES utilisateur(idUtilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE formateur (
  idUtilisateur INT PRIMARY KEY,
  matricule     VARCHAR(100) UNIQUE,
  specialite    VARCHAR(100),
  CONSTRAINT fk_formateur_utilisateur FOREIGN KEY (idUtilisateur) REFERENCES utilisateur(idUtilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE module (
  idModule      INT AUTO_INCREMENT PRIMARY KEY,
  codeModule    VARCHAR(50)  NOT NULL UNIQUE,
  intitule      VARCHAR(150) NOT NULL,
  description   TEXT,
  volumeHoraire INT NOT NULL CHECK (volumeHoraire > 0),
  idFormateur   INT,
  CONSTRAINT fk_module_formateur FOREIGN KEY (idFormateur) REFERENCES formateur(idUtilisateur) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE programmer (
  idModule INT NOT NULL,
  idGroupe INT NOT NULL,
  PRIMARY KEY (idModule, idGroupe),
  CONSTRAINT fk_programmer_module FOREIGN KEY (idModule) REFERENCES module(idModule) ON DELETE CASCADE,
  CONSTRAINT fk_programmer_groupe FOREIGN KEY (idGroupe) REFERENCES groupe(idGroupe) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE seance (
  idSeance    INT AUTO_INCREMENT PRIMARY KEY,
  dateSeance  DATETIME NOT NULL,
  typeSeance  ENUM('Presentiel','Distanciel') NOT NULL DEFAULT 'Presentiel',
  idModule    INT NOT NULL,
  idGroupe    INT NOT NULL,
  idFormateur INT NOT NULL,
  CONSTRAINT fk_seance_module    FOREIGN KEY (idModule)    REFERENCES module(idModule),
  CONSTRAINT fk_seance_groupe    FOREIGN KEY (idGroupe)    REFERENCES groupe(idGroupe),
  CONSTRAINT fk_seance_formateur FOREIGN KEY (idFormateur) REFERENCES formateur(idUtilisateur)
) ENGINE=InnoDB;

CREATE TABLE presence (
  idPresence     INT AUTO_INCREMENT PRIMARY KEY,
  statutPresence ENUM('Present','Absent') NOT NULL,
  idStagiaire    INT NOT NULL,
  idSeance       INT NOT NULL,
  UNIQUE (idStagiaire, idSeance),
  CONSTRAINT fk_presence_stagiaire FOREIGN KEY (idStagiaire) REFERENCES stagiaire(idUtilisateur) ON DELETE CASCADE,
  CONSTRAINT fk_presence_seance    FOREIGN KEY (idSeance)    REFERENCES seance(idSeance) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE evaluation (
  idSeance INT PRIMARY KEY,
  intitule VARCHAR(150) NOT NULL,
  CONSTRAINT fk_evaluation_seance FOREIGN KEY (idSeance) REFERENCES seance(idSeance) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cours (
  idSeance INT PRIMARY KEY,
  titre    VARCHAR(150),
  CONSTRAINT fk_cours_seance FOREIGN KEY (idSeance) REFERENCES seance(idSeance) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE note (
  idNote       INT AUTO_INCREMENT PRIMARY KEY,
  valeur       DECIMAL(4,2) NOT NULL CHECK (valeur BETWEEN 0 AND 20),
  idEvaluation INT NOT NULL,
  idStagiaire  INT NOT NULL,
  UNIQUE (idEvaluation, idStagiaire),
  CONSTRAINT fk_note_evaluation FOREIGN KEY (idEvaluation) REFERENCES evaluation(idSeance) ON DELETE CASCADE,
  CONSTRAINT fk_note_stagiaire  FOREIGN KEY (idStagiaire)  REFERENCES stagiaire(idUtilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- DONNEES REELLES (emploi du temps du groupe DIA_DEVOWFS_TS_203)
-- Mot de passe de tous les comptes : Admin@123
-- Matricules, emails et noms incomplets des stagiaires :
-- valeurs provisoires (a corriger avec la page Modifier)
-- =====================================================

INSERT INTO role (libelle) VALUES ('administrateur'), ('formateur'), ('stagiaire');

INSERT INTO utilisateur (nom, prenom, email, telephone, motDePasse, statut, idRole) VALUES
('ABOULFARAJ', 'AYA', 'aboulfaraj.aya@centre.ma', NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('EL MOUSSABIQ', 'MARWA', 'elmoussabiq.marwa@centre.ma', NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('EL MOUSSABIQ', 'SAFAE', 'elmoussabiq.safae@centre.ma', NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('EL-MANSOUR', 'HICHAM', 'elmansour.hicham@centre.ma', NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('TABTI', 'FATIMA EZZAHRAA', 'tabti.fatimaezzahraa@centre.ma', NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('ZAHRI', 'MERIEM', 'zahri.meriem@centre.ma', NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('-', 'Dounia',    'dounia@centre.ma',    NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('-', 'Amanas',    'amanas@centre.ma',    NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('-', 'Hiba',      'hiba@centre.ma',      NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('-', 'Khadija',   'khadija@centre.ma',   NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('-', 'Sanae',     'sanae@centre.ma',     NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('-', 'Yassine',   'yassine@centre.ma',   NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('-', 'Zineb',     'zineb@centre.ma',     NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('ASSAL', 'ABDELAZIZ', 'assal.abdelaziz@centre.ma', NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3),
('RITY', 'ILYASSE', 'rity.ilyasse@centre.ma', NULL, '$2y$12$xqkMU06e0Zo2SzrUPSa/Hu/EGC6xeiB00MnNg.HlPNv/tQYfwftdS', 'actif', 3);

INSERT INTO groupe (codeGroupe, nom, filiere, annee, capacite, statut) VALUES
('DIA_DEVOWFS_TS_203', 'DIA_DEVOWFS_TS_203', 'Développement Digital', '2ème année',20 , 'actif');

INSERT INTO administrateur (idUtilisateur) VALUES (1);

INSERT INTO formateur (idUtilisateur, matricule, specialite) VALUES
  (2, 'F001', 'Approche agile'),
  (3, 'F002', 'Gestion des données'),
  (4, 'F003', 'Développement front-end');


INSERT INTO stagiaire (idUtilisateur, numInscription, dateNaissance, idGroupe) VALUES
  (5,  'E0001', NULL, 1),
  (6,  'E0002', NULL, 1),
  (7,  'E0003', NULL, 1),
  (8,  'E0004', NULL, 1),
  (9,  'E0005', NULL, 1),
  (10, 'E0006', NULL, 1),
  (11, 'E0007', NULL, 1),
  (12, 'E0008', NULL, 1),
  (13, 'E0009', NULL, 1),
  (14, 'E0010', NULL, 1),
  (15, 'E0011', NULL, 1),
  (16, 'E0012', NULL, 1),
  (17, 'E0013', NULL, 1),
  (18, 'E0014', NULL, 1),
  (19, 'E0015', NULL, 1),
  (20, 'E0016', NULL, 1);


INSERT INTO module (codeModule, intitule, description, volumeHoraire, idFormateur) VALUES
  ('M101', 'Développement front-end', 'Développement front-end', 60, 4),
  ('M102', 'Approche agile',          'Approche agile',          60, 2),
  ('M103', 'Gestion des données',     'Gestion des données',     60, 3);

INSERT INTO programmer (idModule, idGroupe) VALUES
  (1, 1), (2, 1), (3, 1), (4, 1);