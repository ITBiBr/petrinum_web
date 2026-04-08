<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408091931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foto ADD aktuality_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE foto ADD CONSTRAINT FK_EADC3BE52883911 FOREIGN KEY (aktuality_id) REFERENCES aktuality (id)');
        $this->addSql('CREATE INDEX IDX_EADC3BE52883911 ON foto (aktuality_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foto DROP FOREIGN KEY FK_EADC3BE52883911');
        $this->addSql('DROP INDEX IDX_EADC3BE52883911 ON foto');
        $this->addSql('ALTER TABLE foto DROP aktuality_id');
    }
}
