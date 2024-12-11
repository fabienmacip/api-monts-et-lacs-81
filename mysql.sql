-- Création de la base de données
CREATE DATABASE IF NOT EXISTS monts_et_lacs;

USE monts_et_lacs;

-- Activer la fonction UUID() si ce n'est pas déjà le cas
CREATE TABLE IF NOT EXISTS uuid_default (id CHAR(36) PRIMARY KEY DEFAULT (UUID()));

-- Table des utilisateurs (clients enregistrés)
CREATE TABLE IF NOT EXISTS users (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    civility ENUM('M', 'Mme', 'Mx') DEFAULT NULL,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    phone VARCHAR(20),
    role ENUM('guest', 'user', 'admin', 'superadmin') DEFAULT 'guest',
    is_verified BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des produits
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des commandes (inclut les invités et utilisateurs connectés)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(36) NULL,  -- Référence à l'utilisateur via un UUID (NULL pour les invités)
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',  -- Statut de la commande (ex : 'pending', 'completed', etc.)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des éléments de commande (produits associés à une commande)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,  -- Prix au moment de la commande
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table pour gérer les sessions ou jetons d'authentification (si vous en avez besoin pour la gestion des utilisateurs connectés)
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(36), -- UUID pour identifier les utilisateurs
    token VARCHAR(255) NOT NULL, -- Un jeton d'authentification (par exemple JWT)
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

