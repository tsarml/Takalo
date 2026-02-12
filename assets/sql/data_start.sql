-- Mysql
CREATE DATABASE takalo;
use takalo;
-- Users table: stores registered users
CREATE TABLE users (
    id_user SERIAL PRIMARY KEY, 
    email VARCHAR(100) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL 
);

-- Categories table: stores item categories (e.g., clothing, book, DVD)
CREATE TABLE categories (
    id_categorie SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- objects table: stores objects uploaded by users
CREATE TABLE objects (
    id_object SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    id_categorie INTEGER REFERENCES categories(id_categorie) ON DELETE SET NULL,
    id_user INTEGER REFERENCES users(id_user) ON DELETE CASCADE,
    image_url VARCHAR(255), 
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    etat BOOLEAN DEFAULT TRUE  
);

-- Exchange Proposals table: stores exchange requests between users

CREATE TABLE echanges (
    id_echange      SERIAL PRIMARY KEY,
    id_proposer     INTEGER REFERENCES users(id_user) ON DELETE CASCADE,
    id_proposee     INTEGER REFERENCES users(id_user) ON DELETE CASCADE,
    id_object_proposer  INTEGER REFERENCES objects(id_object) ON DELETE CASCADE,
    id_object_proposee  INTEGER REFERENCES objects(id_object) ON DELETE CASCADE,
    status          VARCHAR(20) DEFAULT 'en cours' 
     CHECK (status IN ('en cours', 'accepter', 'rejecter')),
    proposal_date   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response_date   TIMESTAMP NULL DEFAULT NULL,
    UNIQUE (id_object_proposer, id_object_proposee)
);

-- Indexes for performance 
CREATE INDEX idx_objects_owner ON objects(id_user);
CREATE INDEX idx_objects_category ON objects(id_categorie);
CREATE INDEX idx_proposals_proposer ON echanges(id_proposer);
CREATE INDEX idx_proposals_proposee ON echanges(id_proposee);
CREATE INDEX idx_proposals_status ON echanges(status);

-- Test donnees
-- Categories
INSERT INTO categories (nom, description) VALUES
('Clothing', 'Apparel and accessories'),
('Books', 'Printed or digital books'),
('DVDs', 'Movies and video content'),
('Electronics', 'Gadgets and devices');

-- Users
INSERT INTO users (email, pwd) VALUES
('user1', 'user1@example.com'),
('user2', 'user2@example.com');

-- objects
INSERT INTO objects (nom, description, id_categorie, id_user, image_url) VALUES
('T-Shirt', 'Red cotton t-shirt, size M', 1, 1, 'https://example.com/tshirt.jpg'),
('Novel Book', 'Fantasy novel by famous author', 2, 2, 'https://example.com/book.jpg'),
('Action DVD', 'Blockbuster action movie', 3, 1, 'https://example.com/dvd.jpg');

-- Exchange Proposal
INSERT INTO echanges (id_proposer, id_proposee, id_object_proposer, id_object_proposee, status) VALUES
(1, 2, 1, 2, 'en cours');