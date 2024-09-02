-- Créer la base de données
CREATE DATABASE draft_shop;

-- Sélectionner la base de données
USE draft_shop;

-- Création de la table 'category'
CREATE TABLE category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL
);

-- Création de la table 'product'
CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    photos TEXT,  -- Stockage sous forme de JSON ou de chaînes
    price INT NOT NULL,
    description TEXT,
    quantity INT NOT NULL,
    category_id INT,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE SET NULL
);

-- Insertion de données dans la table 'category'
INSERT INTO category (name, description, createdAt, updatedAt) 
VALUES 
('Clothing', 'Men and women clothing', NOW(), NOW()),
('Electronics', 'Electronic gadgets and devices', NOW(), NOW());

-- Insertion de données dans la table 'product'
INSERT INTO product (name, photos, price, description, quantity, category_id, createdAt, updatedAt) 
VALUES 
('T-shirt', '["https://picsum.photos/200/300"]', 1000, 'T-shirt for men', 10, 1, NOW(), NOW()),
('Smartphone', '["https://picsum.photos/200/301"]', 50000, 'Latest smartphone model', 5, 2, NOW(), NOW());
