<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240925144117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuracion_asistencia ADD COLUMN cas_area BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE configuracion_asistencia ADD COLUMN cas_puesto BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE configuracion_asistencia ADD COLUMN cas_estado VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE configuracion_asistencia ADD COLUMN cas_predhorario BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__configuracion_asistencia AS SELECT id, cas_modalidad, cas_eliminado, cas_empresa_id, cas_grupo_id, cas_sede_id FROM configuracion_asistencia');
        $this->addSql('DROP TABLE configuracion_asistencia');
        $this->addSql('CREATE TABLE configuracion_asistencia (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cas_modalidad VARCHAR(255) DEFAULT NULL, cas_eliminado BOOLEAN DEFAULT NULL, cas_empresa_id INTEGER DEFAULT NULL, cas_grupo_id INTEGER DEFAULT NULL, cas_sede_id INTEGER DEFAULT NULL, CONSTRAINT FK_FB765688FB64DBEF FOREIGN KEY (cas_empresa_id) REFERENCES empresa (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FB765688FC776E39 FOREIGN KEY (cas_grupo_id) REFERENCES grupo (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FB765688A54FAE20 FOREIGN KEY (cas_sede_id) REFERENCES sede (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO configuracion_asistencia (id, cas_modalidad, cas_eliminado, cas_empresa_id, cas_grupo_id, cas_sede_id) SELECT id, cas_modalidad, cas_eliminado, cas_empresa_id, cas_grupo_id, cas_sede_id FROM __temp__configuracion_asistencia');
        $this->addSql('DROP TABLE __temp__configuracion_asistencia');
        $this->addSql('CREATE INDEX IDX_FB765688FB64DBEF ON configuracion_asistencia (cas_empresa_id)');
        $this->addSql('CREATE INDEX IDX_FB765688FC776E39 ON configuracion_asistencia (cas_grupo_id)');
        $this->addSql('CREATE INDEX IDX_FB765688A54FAE20 ON configuracion_asistencia (cas_sede_id)');
    }
}
