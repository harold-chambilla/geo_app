<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240921181421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE motivo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, mtv_nombre VARCHAR(64) NOT NULL, mtv_eliminado BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE permiso (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pms_eliminado BOOLEAN NOT NULL, pms_estado VARCHAR(64) DEFAULT NULL, pms_motivo_id INTEGER NOT NULL, pms_colaborador_id INTEGER NOT NULL, CONSTRAINT FK_FD7AAB9E334E2E81 FOREIGN KEY (pms_motivo_id) REFERENCES motivo (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FD7AAB9EC3E7D0D4 FOREIGN KEY (pms_colaborador_id) REFERENCES colaborador (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FD7AAB9E334E2E81 ON permiso (pms_motivo_id)');
        $this->addSql('CREATE INDEX IDX_FD7AAB9EC3E7D0D4 ON permiso (pms_colaborador_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE motivo');
        $this->addSql('DROP TABLE permiso');
    }
}
