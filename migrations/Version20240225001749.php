<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240225001749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asistencia ADD COLUMN asi_fotoentrada VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE asistencia ADD COLUMN asi_fotosalida VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__asistencia AS SELECT id, asi_fechaentrada, asi_fechasalida, asi_horaentrada, asi_horasalida, asi_estadoentrada, asi_estadosalida, asi_ubicacion, asi_eliminado, asi_colaborador_id FROM asistencia');
        $this->addSql('DROP TABLE asistencia');
        $this->addSql('CREATE TABLE asistencia (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, asi_fechaentrada DATE DEFAULT NULL, asi_fechasalida DATE DEFAULT NULL, asi_horaentrada TIME DEFAULT NULL, asi_horasalida TIME DEFAULT NULL, asi_estadoentrada VARCHAR(255) DEFAULT NULL, asi_estadosalida VARCHAR(255) DEFAULT NULL, asi_ubicacion CLOB DEFAULT NULL, asi_eliminado BOOLEAN DEFAULT NULL, asi_colaborador_id INTEGER DEFAULT NULL, CONSTRAINT FK_D8264A8D46F7DF74 FOREIGN KEY (asi_colaborador_id) REFERENCES colaborador (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO asistencia (id, asi_fechaentrada, asi_fechasalida, asi_horaentrada, asi_horasalida, asi_estadoentrada, asi_estadosalida, asi_ubicacion, asi_eliminado, asi_colaborador_id) SELECT id, asi_fechaentrada, asi_fechasalida, asi_horaentrada, asi_horasalida, asi_estadoentrada, asi_estadosalida, asi_ubicacion, asi_eliminado, asi_colaborador_id FROM __temp__asistencia');
        $this->addSql('DROP TABLE __temp__asistencia');
        $this->addSql('CREATE INDEX IDX_D8264A8D46F7DF74 ON asistencia (asi_colaborador_id)');
    }
}
