CREATE TABLE users(
   Id_users SERIAL,
   prenom VARCHAR(255)  NOT NULL,
   date_naissance DATE NOT NULL,
   email VARCHAR(255)  NOT NULL,
   password VARCHAR(255)  NOT NULL,
   nom VARCHAR(255)  NOT NULL,
   solde NUMERIC(19,5),
   date_inscription TIMESTAMP NOT NULL,
   PRIMARY KEY(Id_users),
   UNIQUE(email)
);

CREATE TABLE mouvement_solde(
   Id_mouvement_solde SERIAL,
   somme NUMERIC(19,5) NOT NULL,
   date_mouvement TIMESTAMP NOT NULL,
   est_depot BOOLEAN NOT NULL,
   statut VARCHAR(50)  NOT NULL DEFAULT  'en_attente',
   Id_users INTEGER NOT NULL,
   PRIMARY KEY(Id_mouvement_solde),
   FOREIGN KEY(Id_users) REFERENCES users(Id_users)
);

CREATE TABLE crypto(
   Id_crypto SERIAL,
   intitule VARCHAR(50)  NOT NULL,
   current_valeur NUMERIC(19,5),
   PRIMARY KEY(Id_crypto)
);

CREATE TABLE cour_crypto(
   Id_cour_crypto SERIAL,
   instant TIMESTAMP NOT NULL,
   valeur_dollar NUMERIC(19,5) NOT NULL,
   Id_crypto INTEGER NOT NULL,
   PRIMARY KEY(Id_cour_crypto),
   FOREIGN KEY(Id_crypto) REFERENCES crypto(Id_crypto)
);

CREATE TABLE mouvement_crypto(
   Id_mouvement_crypto SERIAL,
   est_achat BOOLEAN NOT NULL,
   date_mouvement TIMESTAMP NOT NULL,
   quantite NUMERIC(15,8)   NOT NULL,
   valeur_crypto NUMERIC(15,2)   NOT NULL,
   Id_crypto INTEGER NOT NULL,
   Id_users INTEGER NOT NULL,
   PRIMARY KEY(Id_mouvement_crypto),
   FOREIGN KEY(Id_crypto) REFERENCES crypto(Id_crypto),
   FOREIGN KEY(Id_users) REFERENCES users(Id_users)
);

CREATE TABLE token_connexion(
   Id_token_connexion SERIAL,
   code VARCHAR(512)  NOT NULL,
   date_expiration TIMESTAMP NOT NULL,
   Id_users INTEGER NOT NULL,
   PRIMARY KEY(Id_token_connexion),
   UNIQUE(code),
   FOREIGN KEY(Id_users) REFERENCES users(Id_users)
);

CREATE TABLE commission(
   Id_commission SERIAL,
   pourcentage NUMERIC(15,2)   NOT NULL,
   valeur NUMERIC(15,2)   NOT NULL,
   Id_mouvement_crypto INTEGER NOT NULL,
   PRIMARY KEY(Id_commission),
   FOREIGN KEY(Id_mouvement_crypto) REFERENCES mouvement_crypto(Id_mouvement_crypto)
);

CREATE TABLE historique_pourcentage_commission(
   Id_historique_commission SERIAL,
   date_historique_porucentage TIMESTAMP NOT NULL,
   valeur_historique_pourcentage NUMERIC(15,2)   NOT NULL,
   PRIMARY KEY(Id_historique_commission)
);

CREATE TABLE crypto_utilisateur(
   Id_crypto_utilisateur SERIAL,
   quantite NUMERIC(15,8)   NOT NULL,
   Id_users INTEGER NOT NULL,
   Id_crypto INTEGER NOT NULL,
   PRIMARY KEY(Id_crypto_utilisateur),
   FOREIGN KEY(Id_users) REFERENCES users(Id_users),
   FOREIGN KEY(Id_crypto) REFERENCES crypto(Id_crypto)
);

CREATE TABLE users_admin(
   Id_users_admin SERIAL,
   username VARCHAR(255)  NOT NULL,
   password VARCHAR(255)  NOT NULL,
   PRIMARY KEY(Id_users_admin),
   UNIQUE(username)
);

-- trigger pour la mis a jour de la valeur du crypto
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
-- ========================================================================
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
-- ========================================================================


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

INSERT INTO historique_pourcentage_commission (date_historique_porucentage, valeur_historique_pourcentage) 
VALUES (NOW(), 10.00);
INSERT INTO users_admin (username, password)
VALUES ('admin', 'admin');