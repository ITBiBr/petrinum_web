<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408092853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foto DROP FOREIGN KEY `FK_EADC3BE5825396CB`');
        $this->addSql('DROP TABLE galerie');
        $this->addSql('DROP INDEX IDX_EADC3BE5825396CB ON foto');
        $this->addSql('ALTER TABLE foto DROP galerie_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE galerie (id INT AUTO_INCREMENT NOT NULL, nazev VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE foto ADD galerie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE foto ADD CONSTRAINT `FK_EADC3BE5825396CB` FOREIGN KEY (galerie_id) REFERENCES galerie (id)');
        $this->addSql('CREATE INDEX IDX_EADC3BE5825396CB ON foto (galerie_id)');
    }
}
