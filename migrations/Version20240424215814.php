<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424215814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE area (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ara_nombre VARCHAR(255) NOT NULL, ara_eliminado BOOLEAN NOT NULL, ara_empresa_id INTEGER NOT NULL, CONSTRAINT FK_D7943D68F3E4A635 FOREIGN KEY (ara_empresa_id) REFERENCES empresa (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D7943D68F3E4A635 ON area (ara_empresa_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE area');
    }
}
