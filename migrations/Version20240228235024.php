<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228235024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__asistencia AS SELECT id, asi_fechaentrada, asi_fechasalida, asi_horaentrada, asi_horasalida, asi_fotoentrada, asi_fotosalida, asi_estadoentrada, asi_estadosalida, asi_ubicacion, asi_eliminado, asi_colaborador_id FROM asistencia');
        $this->addSql('DROP TABLE asistencia');
        $this->addSql('CREATE TABLE asistencia (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, asi_fechaentrada DATE DEFAULT NULL, asi_fechasalida DATE DEFAULT NULL, asi_horaentrada TIME DEFAULT NULL, asi_horasalida TIME DEFAULT NULL, asi_fotoentrada VARCHAR(255) DEFAULT NULL, asi_fotosalida VARCHAR(255) DEFAULT NULL, asi_estadoentrada VARCHAR(255) DEFAULT NULL, asi_estadosalida VARCHAR(255) DEFAULT NULL, asi_ubicacionentrada CLOB DEFAULT NULL, asi_eliminado BOOLEAN DEFAULT NULL, asi_colaborador_id INTEGER DEFAULT NULL, asi_ubicacionsalida CLOB DEFAULT NULL, asi_notas CLOB DEFAULT NULL, CONSTRAINT FK_D8264A8D46F7DF74 FOREIGN KEY (asi_colaborador_id) REFERENCES colaborador (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO asistencia (id, asi_fechaentrada, asi_fechasalida, asi_horaentrada, asi_horasalida, asi_fotoentrada, asi_fotosalida, asi_estadoentrada, asi_estadosalida, asi_ubicacionentrada, asi_eliminado, asi_colaborador_id) SELECT id, asi_fechaentrada, asi_fechasalida, asi_horaentrada, asi_horasalida, asi_fotoentrada, asi_fotosalida, asi_estadoentrada, asi_estadosalida, asi_ubicacion, asi_eliminado, asi_colaborador_id FROM __temp__asistencia');
        $this->addSql('DROP TABLE __temp__asistencia');
        $this->addSql('CREATE INDEX IDX_D8264A8D46F7DF74 ON asistencia (asi_colaborador_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__colaborador AS SELECT id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_puesto, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id FROM colaborador');
        $this->addSql('DROP TABLE colaborador');
        $this->addSql('CREATE TABLE colaborador (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, col_nombres VARCHAR(255) DEFAULT NULL, col_apellidos VARCHAR(255) DEFAULT NULL, col_dninit VARCHAR(255) DEFAULT NULL, col_fechanacimiento DATE DEFAULT NULL, col_puesto VARCHAR(255) DEFAULT NULL, col_area VARCHAR(255) DEFAULT NULL, col_correoelectronico VARCHAR(255) DEFAULT NULL, roles CLOB NOT NULL, col_nombreusuario VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, col_eliminado BOOLEAN DEFAULT NULL, col_empresa_id INTEGER DEFAULT NULL, col_grupo_id INTEGER DEFAULT NULL, CONSTRAINT FK_D2F80BB37F0EA5D2 FOREIGN KEY (col_empresa_id) REFERENCES empresa (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D2F80BB378A49F5A FOREIGN KEY (col_grupo_id) REFERENCES grupo (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO colaborador (id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_puesto, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id) SELECT id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_puesto, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id FROM __temp__colaborador');
        $this->addSql('DROP TABLE __temp__colaborador');
        $this->addSql('CREATE INDEX IDX_D2F80BB378A49F5A ON colaborador (col_grupo_id)');
        $this->addSql('CREATE INDEX IDX_D2F80BB37F0EA5D2 ON colaborador (col_empresa_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2F80BB331B984AC ON colaborador (col_nombreusuario)');
        $this->addSql('ALTER TABLE horario_trabajo ADD COLUMN hot_fecha DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE horario_trabajo ADD COLUMN hot_tipojornada VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__asistencia AS SELECT id, asi_fechaentrada, asi_fechasalida, asi_horaentrada, asi_horasalida, asi_fotoentrada, asi_fotosalida, asi_estadoentrada, asi_estadosalida, asi_eliminado, asi_colaborador_id FROM asistencia');
        $this->addSql('DROP TABLE asistencia');
        $this->addSql('CREATE TABLE asistencia (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, asi_fechaentrada DATE DEFAULT NULL, asi_fechasalida DATE DEFAULT NULL, asi_horaentrada TIME DEFAULT NULL, asi_horasalida TIME DEFAULT NULL, asi_fotoentrada VARCHAR(255) DEFAULT NULL, asi_fotosalida VARCHAR(255) DEFAULT NULL, asi_estadoentrada VARCHAR(255) DEFAULT NULL, asi_estadosalida VARCHAR(255) DEFAULT NULL, asi_eliminado BOOLEAN DEFAULT NULL, asi_colaborador_id INTEGER DEFAULT NULL, asi_ubicacion CLOB DEFAULT NULL, CONSTRAINT FK_D8264A8D46F7DF74 FOREIGN KEY (asi_colaborador_id) REFERENCES colaborador (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO asistencia (id, asi_fechaentrada, asi_fechasalida, asi_horaentrada, asi_horasalida, asi_fotoentrada, asi_fotosalida, asi_estadoentrada, asi_estadosalida, asi_eliminado, asi_colaborador_id) SELECT id, asi_fechaentrada, asi_fechasalida, asi_horaentrada, asi_horasalida, asi_fotoentrada, asi_fotosalida, asi_estadoentrada, asi_estadosalida, asi_eliminado, asi_colaborador_id FROM __temp__asistencia');
        $this->addSql('DROP TABLE __temp__asistencia');
        $this->addSql('CREATE INDEX IDX_D8264A8D46F7DF74 ON asistencia (asi_colaborador_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__colaborador AS SELECT id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_puesto, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id FROM colaborador');
        $this->addSql('DROP TABLE colaborador');
        $this->addSql('CREATE TABLE colaborador (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, col_nombres VARCHAR(255) DEFAULT NULL, col_apellidos VARCHAR(255) DEFAULT NULL, col_dninit VARCHAR(180) NOT NULL, col_fechanacimiento DATE DEFAULT NULL, col_puesto VARCHAR(255) DEFAULT NULL, col_area VARCHAR(255) DEFAULT NULL, col_correoelectronico VARCHAR(255) DEFAULT NULL, roles CLOB NOT NULL, col_nombreusuario VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, col_eliminado BOOLEAN DEFAULT NULL, col_empresa_id INTEGER DEFAULT NULL, col_grupo_id INTEGER DEFAULT NULL, CONSTRAINT FK_D2F80BB37F0EA5D2 FOREIGN KEY (col_empresa_id) REFERENCES empresa (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D2F80BB378A49F5A FOREIGN KEY (col_grupo_id) REFERENCES grupo (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO colaborador (id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_puesto, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id) SELECT id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_puesto, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id FROM __temp__colaborador');
        $this->addSql('DROP TABLE __temp__colaborador');
        $this->addSql('CREATE INDEX IDX_D2F80BB37F0EA5D2 ON colaborador (col_empresa_id)');
        $this->addSql('CREATE INDEX IDX_D2F80BB378A49F5A ON colaborador (col_grupo_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2F80BB35A0E0AD2 ON colaborador (col_dninit)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__horario_trabajo AS SELECT id, hot_diasemana, hot_horaentrada, hot_horasalida, hot_eliminado, hot_colaborador_id FROM horario_trabajo');
        $this->addSql('DROP TABLE horario_trabajo');
        $this->addSql('CREATE TABLE horario_trabajo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, hot_diasemana VARCHAR(255) DEFAULT NULL, hot_horaentrada TIME DEFAULT NULL, hot_horasalida TIME DEFAULT NULL, hot_eliminado BOOLEAN DEFAULT NULL, hot_colaborador_id INTEGER DEFAULT NULL, CONSTRAINT FK_DA85417D3D04A72 FOREIGN KEY (hot_colaborador_id) REFERENCES colaborador (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO horario_trabajo (id, hot_diasemana, hot_horaentrada, hot_horasalida, hot_eliminado, hot_colaborador_id) SELECT id, hot_diasemana, hot_horaentrada, hot_horasalida, hot_eliminado, hot_colaborador_id FROM __temp__horario_trabajo');
        $this->addSql('DROP TABLE __temp__horario_trabajo');
        $this->addSql('CREATE INDEX IDX_DA85417D3D04A72 ON horario_trabajo (hot_colaborador_id)');
    }
}
