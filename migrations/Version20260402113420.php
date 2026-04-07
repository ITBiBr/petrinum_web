<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260402113420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE foto (id INT AUTO_INCREMENT NOT NULL, nazev VARCHAR(255) NOT NULL, soubor VARCHAR(255) NOT NULL, galerie_id INT DEFAULT NULL, INDEX IDX_EADC3BE5825396CB (galerie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE galerie (id INT AUTO_INCREMENT NOT NULL, nazev VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE foto ADD CONSTRAINT FK_EADC3BE5825396CB FOREIGN KEY (galerie_id) REFERENCES galerie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foto DROP FOREIGN KEY FK_EADC3BE5825396CB');
        $this->addSql('DROP TABLE foto');
        $this->addSql('DROP TABLE galerie');
    }
}
