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