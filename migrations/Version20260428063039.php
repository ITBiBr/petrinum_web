<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260428063039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE zamestnanci (id INT AUTO_INCREMENT NOT NULL, jmeno VARCHAR(255) NOT NULL, role VARCHAR(255) DEFAULT NULL, foto VARCHAR(255) DEFAULT NULL, zamestnanci_kategorie_id INT DEFAULT NULL, INDEX IDX_AC54C760B16E5C42 (zamestnanci_kategorie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE zamestnanci ADD CONSTRAINT FK_AC54C760B16E5C42 FOREIGN KEY (zamestnanci_kategorie_id) REFERENCES zamestnanci_kategorie (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zamestnanci DROP FOREIGN KEY FK_AC54C760B16E5C42');
        $this->addSql('DROP TABLE zamestnanci');
    }
}
