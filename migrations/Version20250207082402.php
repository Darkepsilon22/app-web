<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207082402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE users_admin_id_users_admin_seq CASCADE');
        $this->addSql('DROP SEQUENCE token_connexion_id_token_connexion_seq CASCADE');
        $this->addSql('ALTER TABLE token_connexion DROP CONSTRAINT token_connexion_id_users_fkey');
        $this->addSql('DROP TABLE token_connexion');
        $this->addSql('ALTER TABLE crypto ALTER current_valeur TYPE NUMERIC(15, 2)');
        $this->addSql('ALTER TABLE crypto_utilisateur ALTER quantite TYPE NUMERIC(15, 2)');
        $this->addSql('ALTER TABLE mouvement_solde ALTER somme TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE mouvement_solde ALTER date_mouvement TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN mouvement_solde.date_mouvement IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE users ALTER solde TYPE NUMERIC(15, 2)');
        $this->addSql('ALTER TABLE users ALTER date_inscription TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN users.date_inscription IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER INDEX users_email_key RENAME TO UNIQ_1483A5E9E7927C74');
        $this->addSql('ALTER TABLE users_admin DROP CONSTRAINT users_admin_pkey');
        $this->addSql('ALTER TABLE users_admin RENAME COLUMN id_users_admin TO id');
        $this->addSql('ALTER TABLE users_admin ADD PRIMARY KEY (id)');
        $this->addSql('ALTER INDEX users_admin_username_key RENAME TO UNIQ_6F79A93FF85E0677');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE users_admin_id_users_admin_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE token_connexion_id_token_connexion_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE token_connexion (id_token_connexion SERIAL NOT NULL, id_users INT NOT NULL, code VARCHAR(255) NOT NULL, date_expiration TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id_token_connexion))');
        $this->addSql('CREATE UNIQUE INDEX token_connexion_code_key ON token_connexion (code)');
        $this->addSql('CREATE INDEX IDX_E7AE74DBFA06E4D9 ON token_connexion (id_users)');
        $this->addSql('ALTER TABLE token_connexion ADD CONSTRAINT token_connexion_id_users_fkey FOREIGN KEY (id_users) REFERENCES users (id_users) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE crypto ALTER current_valeur TYPE NUMERIC(19, 5)');
        $this->addSql('ALTER TABLE mouvement_solde ALTER somme TYPE NUMERIC(19, 5)');
        $this->addSql('ALTER TABLE mouvement_solde ALTER date_mouvement TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN mouvement_solde.date_mouvement IS NULL');
        $this->addSql('DROP INDEX users_admin_pkey');
        $this->addSql('ALTER TABLE users_admin RENAME COLUMN id TO id_users_admin');
        $this->addSql('ALTER TABLE users_admin ADD PRIMARY KEY (id_users_admin)');
        $this->addSql('ALTER INDEX uniq_6f79a93ff85e0677 RENAME TO users_admin_username_key');
        $this->addSql('ALTER TABLE users ALTER solde TYPE NUMERIC(19, 5)');
        $this->addSql('ALTER TABLE users ALTER date_inscription TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN users.date_inscription IS NULL');
        $this->addSql('ALTER INDEX uniq_1483a5e9e7927c74 RENAME TO users_email_key');
        $this->addSql('ALTER TABLE crypto_utilisateur ALTER quantite TYPE NUMERIC(15, 8)');
    }
}
