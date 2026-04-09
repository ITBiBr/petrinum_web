<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260409095655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE akce ADD perex LONGTEXT DEFAULT NULL, ADD video VARCHAR(255) DEFAULT NULL, CHANGE `text1` `obsah` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL, CHANGE `text2` `obsah_pokracovani` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE akce ADD text1 LONGTEXT DEFAULT NULL, ADD text2 LONGTEXT DEFAULT NULL, DROP perex, DROP video, DROP obsah, DROP obsah_pokracovani');
    }
}
