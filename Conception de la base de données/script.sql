CREATE TABLE Utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL ,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'client'
) ;

CREATE TABLE Voiture (
    id_voiture INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    type_carburant VARCHAR(30) NOT NULL,
    boite_vitesse VARCHAR(30) NOT NULL,
    tarif_journalier DECIMAL(10,2) NOT NULL,
    disponibilite VARCHAR(20) NOT NULL DEFAULT 'disponible',
    image VARCHAR(255),
    annee INT NOT NULL
) ;

CREATE TABLE Reservation (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    statut_reservation ENUM('En attente', 'Confirmée', 'Annulée') NOT NULL DEFAULT 'En attente'
    id_utilisateur INT NOT NULL,
    id_voiture INT NOT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ,
    FOREIGN KEY (id_voiture) REFERENCES Voiture(id_voiture)
);