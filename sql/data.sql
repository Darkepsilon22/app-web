-- Active: 1726508341924@@127.0.0.1@3306@appcrypto
    

INSERT INTO users (prenom, nom, date_naissance, email, password, solde, date_inscription)
VALUES
('John', 'Doe', '1990-01-01', 'johndoe@example.com', 'password123', 1000.00, CURRENT_TIMESTAMP);


INSERT INTO mouvement_solde (somme, date_mouvement, est_depot, Id_users)
VALUES
(5000.00, CURRENT_TIMESTAMP, TRUE, 1); 

UPDATE users
SET solde = 50000.00
WHERE Id_users = 1;




-- SELECT * FROM cour_crypto WHERE id_crypto=1;