-- =====================================================
-- Base de données QuizMaster
-- Script SQL simple pour les débutants
-- =====================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS quiz_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quiz_platform;

-- =====================================================
-- Table: users (Utilisateurs)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('enseignant', 'etudiant') DEFAULT 'enseignant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

-- =====================================================
-- Table: categories (Catégories de quiz)
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    enseignant_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enseignant_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- Table: quiz (Les quiz)
-- =====================================================
CREATE TABLE IF NOT EXISTS quiz (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    categorie_id INT NOT NULL,
    enseignant_id INT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (enseignant_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- Table: questions (Les questions des quiz)
-- =====================================================
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question TEXT NOT NULL,
    option1 VARCHAR(255) NOT NULL,
    option2 VARCHAR(255) NOT NULL,
    option3 VARCHAR(255) NOT NULL,
    option4 VARCHAR(255) NOT NULL,
    correct_option TINYINT NOT NULL CHECK (correct_option BETWEEN 1 AND 4),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
);

-- =====================================================
-- Table: results (Résultats des quiz - US7)
-- =====================================================
CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    etudiant_id INT NOT NULL,
    score INT NOT NULL DEFAULT 0,
    total_questions INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE,
    FOREIGN KEY (etudiant_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Index pour améliorer les performances de recherche
CREATE INDEX idx_results_etudiant ON results(etudiant_id);
CREATE INDEX idx_results_quiz ON results(quiz_id);
CREATE INDEX idx_quiz_active ON quiz(is_active);

-- =====================================================
-- Données de test (optionnel)
-- =====================================================

-- Créer un enseignant de test
-- Mot de passe: password123 (hashé avec bcrypt)
INSERT INTO users (nom, email, password_hash, role) VALUES 
('Prof Demo', 'prof@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'enseignant');

-- Créer un étudiant de test
INSERT INTO users (nom, email, password_hash, role) VALUES 
('Etudiant Test', 'etudiant@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant');

-- Créer une catégorie de test
INSERT INTO categories (nom, description, enseignant_id) VALUES 
('Mathématiques', 'Quiz de mathématiques', 1);

-- Créer un quiz de test
INSERT INTO quiz (titre, description, categorie_id, enseignant_id, is_active) VALUES 
('Quiz Addition', 'Test vos connaissances en addition', 1, 1, 1);

-- Créer des questions de test
INSERT INTO questions (quiz_id, question, option1, option2, option3, option4, correct_option) VALUES 
(1, 'Combien fait 2 + 2 ?', '3', '4', '5', '6', 2),
(1, 'Combien fait 5 + 3 ?', '7', '8', '9', '10', 2),
(1, 'Combien fait 10 + 5 ?', '13', '14', '15', '16', 3);

-- Créer des résultats de test pour l'étudiant
INSERT INTO results (quiz_id, etudiant_id, score, total_questions) VALUES 
(1, 2, 2, 3),
(1, 2, 3, 3);

