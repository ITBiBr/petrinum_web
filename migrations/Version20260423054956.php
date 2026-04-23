<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260423054956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prilohy (id INT AUTO_INCREMENT NOT NULL, nazev VARCHAR(255) NOT NULL, soubor VARCHAR(255) NOT NULL, position INT NOT NULL, clanky_id INT DEFAULT NULL, INDEX IDX_154A5A327A498028 (clanky_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE prilohy ADD CONSTRAINT FK_154A5A327A498028 FOREIGN KEY (clanky_id) REFERENCES clanky (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prilohy DROP FOREIGN KEY FK_154A5A327A498028');
        $this->addSql('DROP TABLE prilohy');
    }
}
