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



-- UPDATE users
-- SET solde = 75000.00
-- WHERE Id_users = 1;




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

INSERT INTO crypto_utilisateur (quantite, id_users, id_crypto)
VALUES 
    (0.5, 1, 1),  -- 0.5 Bitcoin pour l'utilisateur 1
    (1.0, 1, 2),  -- 1 Ethereum pour l'utilisateur 1
    (10.0, 1, 3), -- 10 Binance Coin pour l'utilisateur 1
    (100.0, 1, 4); -- 100 Ripple pour l'utilisateur 1
