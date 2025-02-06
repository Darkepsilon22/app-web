DROP DATABASE IF EXISTS crypto;
CREATE DATABASE crypto;
\c crypto;

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

CREATE TABLE users_admin(
   Id_users_admin SERIAL,
   username VARCHAR(255)  NOT NULL,
   password VARCHAR(255)  NOT NULL,
   PRIMARY KEY(Id_users_admin),
   UNIQUE(username)
);

CREATE TABLE mouvement_solde(
   Id_mouvement_solde SERIAL,
   somme NUMERIC(19,5) NOT NULL,
   date_mouvement TIMESTAMP NOT NULL,
   est_depot BOOLEAN NOT NULL,
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
   Id_crypto INTEGER NOT NULL,
   Id_users INTEGER NOT NULL,
   PRIMARY KEY(Id_mouvement_crypto),
   FOREIGN KEY(Id_crypto) REFERENCES crypto(Id_crypto),
   FOREIGN KEY(Id_users) REFERENCES users(Id_users)
);

CREATE TABLE token_connexion(
   Id_token_connexion SERIAL,
   code VARCHAR(255)  NOT NULL,
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

CREATE TABLE historique_commission(
   Id_historique_commission SERIAL,
   date_historique TIMESTAMP NOT NULL,
   valeur_historique NUMERIC(15,2)   NOT NULL,
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


