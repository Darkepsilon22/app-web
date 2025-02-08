-- Active: 1731263504649@@127.0.0.1@5432@crypto
INSERT INTO crypto (intitule, current_valeur) VALUES
('Bitcoin', 50000.00),
('Ethereum', 4000.00),
('Binance Coin', 350.00),
('Ripple', 1.00),
('Cardano', 1.20),
('Solana', 150.00),
('Polkadot', 25.00),
('Dogecoin', 0.30),
('Litecoin', 180.00),
('Chainlink', 30.00);    

INSERT INTO users (prenom, nom, date_naissance, email, password, solde, date_inscription)
VALUES
('John', 'Doe', '1990-01-01', 'johndoe@example.com', 'password123', 1000.00, CURRENT_TIMESTAMP);
INSERT INTO historique_pourcentage_commission (date_historique_porucentage, valeur_historique_pourcentage) 
VALUES (NOW(), 10.00);
INSERT INTO users_admin (username, password)
VALUES ('admin', 'admin');

INSERT INTO crypto_utilisateur (quantite, id_users, id_crypto)
VALUES 
    (0.5, 1, 1),  -- 0.5 Bitcoin pour l'utilisateur 1
    (1.0, 1, 2),  -- 1 Ethereum pour l'utilisateur 1
    (10.0, 1, 3), -- 10 Binance Coin pour l'utilisateur 1
    (100.0, 1, 4); -- 100 Ripple pour l'utilisateur 1
