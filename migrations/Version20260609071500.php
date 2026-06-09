<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260609071500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // FULLTEXT indexy
        $this->addSql('
            CREATE FULLTEXT INDEX ft_akce_search
            ON akce (titulek, obsah, obsah_pokracovani)
        ');

        $this->addSql('
            CREATE FULLTEXT INDEX ft_clanky_search
            ON clanky (titulek, obsah, obsah_pokracovani)
        ');
    }

    public function down(Schema $schema): void
    {
        // Smazání FULLTEXT indexů
        $this->addSql('DROP INDEX ft_akce_search ON akce');
        $this->addSql('DROP INDEX ft_clanky_search ON clanky');
    }
}
