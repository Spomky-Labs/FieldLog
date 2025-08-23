<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250823142727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entry (id VARCHAR(255) NOT NULL, title VARCHAR(180) NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(16) NOT NULL, file_path VARCHAR(255) DEFAULT NULL, text_content TEXT DEFAULT NULL, url VARCHAR(2048) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN entry.published_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE entry');
    }
}
