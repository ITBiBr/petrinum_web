<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260413044334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clanky (id INT AUTO_INCREMENT NOT NULL, obsah LONGTEXT NOT NULL, titulek VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, video VARCHAR(255) DEFAULT NULL, obsah_pokracovani LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE foto ADD clanky_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE foto ADD CONSTRAINT FK_EADC3BE57A498028 FOREIGN KEY (clanky_id) REFERENCES clanky (id)');
        $this->addSql('CREATE INDEX IDX_EADC3BE57A498028 ON foto (clanky_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE clanky');
        $this->addSql('ALTER TABLE foto DROP FOREIGN KEY FK_EADC3BE57A498028');
        $this->addSql('DROP INDEX IDX_EADC3BE57A498028 ON foto');
        $this->addSql('ALTER TABLE foto DROP clanky_id');
    }
}
