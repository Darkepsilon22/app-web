-- Active: 1726508341924@@127.0.0.1@3306@appcrypto

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
(50000.00, CURRENT_TIMESTAMP, TRUE, 1); 

UPDATE users
SET solde = 55000.00
WHERE Id_users = 1;




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

INSERT INTO historique_pourcentage_commission (date_historique_porucentage, valeur_historique_pourcentage) 
VALUES (NOW(), 10.00);

-- trigger pour hacher le mot de passe dans la base

-- Activer l'extension pgcrypto si elle n'est pas encore activée
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Créer une fonction qui hash le mot de passe avec bcrypt
CREATE OR REPLACE FUNCTION hash_password_before_insert()
RETURNS TRIGGER AS $$
BEGIN
    -- Si le mot de passe n'est pas déjà haché (éviter de re-hasher plusieurs fois)
    IF NEW.password NOT LIKE '$2y$%' THEN
        NEW.password = crypt(NEW.password, gen_salt('bf'));
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_hash_password
BEFORE INSERT ON users_admin
FOR EACH ROW
EXECUTE FUNCTION hash_password_before_insert();

INSERT INTO users_admin (username, password)
VALUES ('admin', 'admin');


-- SELECT * FROM cour_crypto WHERE id_crypto=1;