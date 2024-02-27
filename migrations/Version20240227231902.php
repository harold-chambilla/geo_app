<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227231902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asistencia (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, asi_fechaentrada DATE DEFAULT NULL, asi_fechasalida DATE DEFAULT NULL, asi_horaentrada TIME DEFAULT NULL, asi_horasalida TIME DEFAULT NULL, asi_fotoentrada VARCHAR(255) DEFAULT NULL, asi_fotosalida VARCHAR(255) DEFAULT NULL, asi_ubicacionentrada CLOB DEFAULT NULL, asi_ubicacionsalida CLOB DEFAULT NULL, asi_estadoentrada VARCHAR(255) DEFAULT NULL, asi_estadosalida VARCHAR(255) DEFAULT NULL, asi_notas CLOB DEFAULT NULL, asi_eliminado BOOLEAN DEFAULT NULL, asi_colaborador_id INTEGER DEFAULT NULL, CONSTRAINT FK_D8264A8D46F7DF74 FOREIGN KEY (asi_colaborador_id) REFERENCES colaborador (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D8264A8D46F7DF74 ON asistencia (asi_colaborador_id)');
        $this->addSql('CREATE TABLE colaborador (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, col_nombres VARCHAR(255) DEFAULT NULL, col_apellidos VARCHAR(255) DEFAULT NULL, col_dninit VARCHAR(255) DEFAULT NULL, col_fechanacimiento DATE DEFAULT NULL, col_puesto VARCHAR(255) DEFAULT NULL, col_area VARCHAR(255) DEFAULT NULL, col_correoelectronico VARCHAR(255) DEFAULT NULL, roles CLOB NOT NULL, col_nombreusuario VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, col_eliminado BOOLEAN DEFAULT NULL, col_empresa_id INTEGER DEFAULT NULL, col_grupo_id INTEGER DEFAULT NULL, CONSTRAINT FK_D2F80BB37F0EA5D2 FOREIGN KEY (col_empresa_id) REFERENCES empresa (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D2F80BB378A49F5A FOREIGN KEY (col_grupo_id) REFERENCES grupo (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2F80BB331B984AC ON colaborador (col_nombreusuario)');
        $this->addSql('CREATE INDEX IDX_D2F80BB37F0EA5D2 ON colaborador (col_empresa_id)');
        $this->addSql('CREATE INDEX IDX_D2F80BB378A49F5A ON colaborador (col_grupo_id)');
        $this->addSql('CREATE TABLE configuracion_asistencia (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cas_modalidad VARCHAR(255) DEFAULT NULL, cas_eliminado BOOLEAN DEFAULT NULL, cas_empresa_id INTEGER DEFAULT NULL, cas_grupo_id INTEGER DEFAULT NULL, cas_sede_id INTEGER DEFAULT NULL, CONSTRAINT FK_FB765688FB64DBEF FOREIGN KEY (cas_empresa_id) REFERENCES empresa (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FB765688FC776E39 FOREIGN KEY (cas_grupo_id) REFERENCES grupo (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FB765688A54FAE20 FOREIGN KEY (cas_sede_id) REFERENCES sede (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FB765688FB64DBEF ON configuracion_asistencia (cas_empresa_id)');
        $this->addSql('CREATE INDEX IDX_FB765688FC776E39 ON configuracion_asistencia (cas_grupo_id)');
        $this->addSql('CREATE INDEX IDX_FB765688A54FAE20 ON configuracion_asistencia (cas_sede_id)');
        $this->addSql('CREATE TABLE empresa (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, emp_ruc VARCHAR(255) DEFAULT NULL, emp_nombreempresa VARCHAR(255) DEFAULT NULL, emp_industria VARCHAR(255) DEFAULT NULL, emp_telefono VARCHAR(255) DEFAULT NULL, emp_cantidadempleados INTEGER DEFAULT NULL, emp_eliminado BOOLEAN DEFAULT NULL)');
        $this->addSql('CREATE TABLE grupo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, grp_nombre VARCHAR(255) DEFAULT NULL, grp_descripcion VARCHAR(255) DEFAULT NULL, grp_eliminado BOOLEAN DEFAULT NULL, grp_empresa_id INTEGER DEFAULT NULL, CONSTRAINT FK_8C0E9BD39C9B4C10 FOREIGN KEY (grp_empresa_id) REFERENCES empresa (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8C0E9BD39C9B4C10 ON grupo (grp_empresa_id)');
        $this->addSql('CREATE TABLE horario_trabajo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, hot_diasemana VARCHAR(255) DEFAULT NULL, hot_fecha DATE DEFAULT NULL, hot_horaentrada TIME DEFAULT NULL, hot_horasalida TIME DEFAULT NULL, hot_tipojornada VARCHAR(255) DEFAULT NULL, hot_eliminado BOOLEAN DEFAULT NULL, hot_colaborador_id INTEGER DEFAULT NULL, CONSTRAINT FK_DA85417D3D04A72 FOREIGN KEY (hot_colaborador_id) REFERENCES colaborador (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DA85417D3D04A72 ON horario_trabajo (hot_colaborador_id)');
        $this->addSql('CREATE TABLE registro_cambios (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reg_tablaafectada VARCHAR(255) DEFAULT NULL, reg_campoafectado VARCHAR(255) DEFAULT NULL, reg_valoranterior VARCHAR(255) DEFAULT NULL, reg_valornuevo VARCHAR(255) DEFAULT NULL, reg_fecha DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE sede (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, sed_nombre VARCHAR(255) DEFAULT NULL, sed_pais VARCHAR(255) DEFAULT NULL, sed_direccion VARCHAR(255) DEFAULT NULL, sed_ubicacion CLOB DEFAULT NULL, sed_eliminado BOOLEAN DEFAULT NULL, sed_empresa_id INTEGER DEFAULT NULL, CONSTRAINT FK_2A9BE2D125EF8966 FOREIGN KEY (sed_empresa_id) REFERENCES empresa (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2A9BE2D125EF8966 ON sede (sed_empresa_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE asistencia');
        $this->addSql('DROP TABLE colaborador');
        $this->addSql('DROP TABLE configuracion_asistencia');
        $this->addSql('DROP TABLE empresa');
        $this->addSql('DROP TABLE grupo');
        $this->addSql('DROP TABLE horario_trabajo');
        $this->addSql('DROP TABLE registro_cambios');
        $this->addSql('DROP TABLE sede');
    }
}
