<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260409070850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE akce (id INT AUTO_INCREMENT NOT NULL, titulek VARCHAR(255) NOT NULL, datum_vlozeni DATETIME NOT NULL, text1 LONGTEXT DEFAULT NULL, text2 LONGTEXT DEFAULT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE foto (id INT AUTO_INCREMENT NOT NULL, nazev VARCHAR(255) NOT NULL, soubor VARCHAR(255) NOT NULL, position INT NOT NULL, akce_id INT DEFAULT NULL, INDEX IDX_EADC3BE599F47138 (akce_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE foto ADD CONSTRAINT FK_EADC3BE599F47138 FOREIGN KEY (akce_id) REFERENCES akce (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foto DROP FOREIGN KEY FK_EADC3BE599F47138');
        $this->addSql('DROP TABLE akce');
        $this->addSql('DROP TABLE foto');
        $this->addSql('DROP TABLE user');
    }
}
