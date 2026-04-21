<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260421043130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu ADD clanky_id INT DEFAULT NULL, DROP route_params');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A937A498028 FOREIGN KEY (clanky_id) REFERENCES clanky (id)');
        $this->addSql('CREATE INDEX IDX_7D053A937A498028 ON menu (clanky_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A937A498028');
        $this->addSql('DROP INDEX IDX_7D053A937A498028 ON menu');
        $this->addSql('ALTER TABLE menu ADD route_params JSON DEFAULT NULL, DROP clanky_id');
    }
}
