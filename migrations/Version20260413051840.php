<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260413051840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stitky (id INT AUTO_INCREMENT NOT NULL, titulek VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE stitky_akce (stitky_id INT NOT NULL, akce_id INT NOT NULL, INDEX IDX_80F00D71E875875D (stitky_id), INDEX IDX_80F00D7199F47138 (akce_id), PRIMARY KEY (stitky_id, akce_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE stitky_akce ADD CONSTRAINT FK_80F00D71E875875D FOREIGN KEY (stitky_id) REFERENCES stitky (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stitky_akce ADD CONSTRAINT FK_80F00D7199F47138 FOREIGN KEY (akce_id) REFERENCES akce (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stitky_akce DROP FOREIGN KEY FK_80F00D71E875875D');
        $this->addSql('ALTER TABLE stitky_akce DROP FOREIGN KEY FK_80F00D7199F47138');
        $this->addSql('DROP TABLE stitky');
        $this->addSql('DROP TABLE stitky_akce');
    }
}
