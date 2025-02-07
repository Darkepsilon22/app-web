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


INSERT INTO mouvement_solde (somme, date_mouvement, est_depot, Id_users)
VALUES
(5000.00, CURRENT_TIMESTAMP, TRUE, 1); 

UPDATE users
SET solde = 50000.00
WHERE Id_users = 1;

INSERT INTO users_admin (username, password)
VALUES ('admin', 'test');


CREATE OR REPLACE FUNCTION update_current_valeur_func()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE crypto
    SET current_valeur = NEW.valeur_dollar
    WHERE Id_crypto = NEW.Id_crypto;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_current_valeur
AFTER INSERT ON cour_crypto
FOR EACH ROW
EXECUTE FUNCTION update_current_valeur_func();


-- SELECT * FROM cour_crypto WHERE id_crypto=1;

INSERT INTO crypto_utilisateur (quantite, id_users, id_crypto)
VALUES 
    (0.5, 1, 1),  -- 0.5 Bitcoin pour l'utilisateur 1
    (1.0, 1, 2),  -- 1 Ethereum pour l'utilisateur 1
    (10.0, 1, 3), -- 10 Binance Coin pour l'utilisateur 1
    (100.0, 1, 4); -- 100 Ripple pour l'utilisateur 1
