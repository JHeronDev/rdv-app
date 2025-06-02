CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    mot_de_passe VARCHAR(255),
    role ENUM('utilisateur', 'admin') DEFAULT 'utilisateur'
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    duree INT,
    prix DECIMAL(6,2)
);

CREATE TABLE plages_horaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE,
    heure TIME
);

CREATE TABLE rdv (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    service_id INT,
    date DATE,
    heure TIME,
    statut ENUM('confirmé', 'annulé', 'modifié') DEFAULT 'confirmé',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);
