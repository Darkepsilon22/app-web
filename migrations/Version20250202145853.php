<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202145853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cour_crypto (id_cour_crypto INT AUTO_INCREMENT NOT NULL, id_crypto INT NOT NULL, instant DATETIME NOT NULL, valeur_dollar NUMERIC(19, 5) NOT NULL, INDEX IDX_DA6B1E274E1F9D68 (id_crypto), PRIMARY KEY(id_cour_crypto)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crypto (id_crypto INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(50) NOT NULL, current_valeur NUMERIC(19, 5) DEFAULT NULL, PRIMARY KEY(id_crypto)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cour_crypto ADD CONSTRAINT FK_DA6B1E274E1F9D68 FOREIGN KEY (id_crypto) REFERENCES crypto (id_crypto)');
        $this->addSql('
            CREATE TRIGGER update_current_valeur
            AFTER INSERT ON cour_crypto
            FOR EACH ROW
            BEGIN
                UPDATE crypto
                SET current_valeur = NEW.valeur_dollar
                WHERE Id_crypto = NEW.Id_crypto;
            END
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cour_crypto DROP FOREIGN KEY FK_DA6B1E274E1F9D68');
        $this->addSql('DROP TABLE cour_crypto');
        $this->addSql('DROP TABLE crypto');
        $this->addSql('DROP TRIGGER IF EXISTS update_current_valeur;');
    }
}
