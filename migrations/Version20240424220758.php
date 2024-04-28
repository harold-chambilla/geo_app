<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424220758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE puesto (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pst_nombre VARCHAR(255) NOT NULL, pst_eliminado BOOLEAN NOT NULL, pst_area_id INTEGER NOT NULL, CONSTRAINT FK_47C3D2DE89F131D5 FOREIGN KEY (pst_area_id) REFERENCES area (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_47C3D2DE89F131D5 ON puesto (pst_area_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE puesto');
    }
}
