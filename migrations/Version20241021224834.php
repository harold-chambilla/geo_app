<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021224834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE area (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ara_nombre VARCHAR(255) NOT NULL, ara_eliminado BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE asistencia (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, asi_fechaentrada DATETIME NOT NULL, asi_fechasalida DATETIME NOT NULL, asi_horaentrada TIME NOT NULL, asi_horasalida TIME NOT NULL, asi_fotoentrada VARCHAR(255) NOT NULL, asi_fotosalida VARCHAR(255) NOT NULL, asi_ubicacionentrada VARCHAR(255) NOT NULL, asi_ubicacionsalida VARCHAR(255) NOT NULL, asi_estadoentrada VARCHAR(255) NOT NULL, asi_estadosalida VARCHAR(255) NOT NULL, asi_notas VARCHAR(255) NOT NULL, asi_eliminado BOOLEAN NOT NULL, colaborador_id INTEGER NOT NULL, CONSTRAINT FK_D8264A8DF1CB264E FOREIGN KEY (colaborador_id) REFERENCES colaborador (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D8264A8DF1CB264E ON asistencia (colaborador_id)');
        $this->addSql('CREATE TABLE colaborador (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, col_nombreusuario VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_COL_NOMBREUSUARIO ON colaborador (col_nombreusuario)');
        $this->addSql('CREATE TABLE configuracion_asistencia (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cas_tiempo_falta_horas INTEGER NOT NULL, cas_tolerancia_ingreso_minutos INTEGER NOT NULL, cas_permitir_foto BOOLEAN NOT NULL, cas_faltas_tardanzas BOOLEAN NOT NULL, cas_permisos BOOLEAN NOT NULL, cas_vacaciones BOOLEAN NOT NULL, cas_marcacion BOOLEAN NOT NULL, cas_modalidad VARCHAR(255) NOT NULL, cas_area BOOLEAN NOT NULL, cas_puesto BOOLEAN NOT NULL, cas_predhorario BOOLEAN NOT NULL, cas_eliminado BOOLEAN NOT NULL, cas_estado VARCHAR(255) NOT NULL, grupo_id INTEGER NOT NULL, sede_id INTEGER NOT NULL, puesto_id INTEGER NOT NULL, CONSTRAINT FK_FB7656889C833003 FOREIGN KEY (grupo_id) REFERENCES grupo (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FB765688E19F41BF FOREIGN KEY (sede_id) REFERENCES sede (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FB7656885035E9DA FOREIGN KEY (puesto_id) REFERENCES puesto (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FB7656889C833003 ON configuracion_asistencia (grupo_id)');
        $this->addSql('CREATE INDEX IDX_FB765688E19F41BF ON configuracion_asistencia (sede_id)');
        $this->addSql('CREATE INDEX IDX_FB7656885035E9DA ON configuracion_asistencia (puesto_id)');
        $this->addSql('CREATE TABLE empresa (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, emp_ruc VARCHAR(255) NOT NULL, emp_nombreempresa VARCHAR(255) NOT NULL, emp_industria VARCHAR(255) NOT NULL, emp_telefono VARCHAR(255) NOT NULL, emp_cantidadempleados INTEGER NOT NULL, emp_eliminado BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE grupo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, grp_nombre VARCHAR(255) NOT NULL, grp_descripcion VARCHAR(255) NOT NULL, grp_eliminado BOOLEAN NOT NULL, empresa_id INTEGER NOT NULL, CONSTRAINT FK_8C0E9BD3521E1991 FOREIGN KEY (empresa_id) REFERENCES empresa (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8C0E9BD3521E1991 ON grupo (empresa_id)');
        $this->addSql('CREATE TABLE horario_trabajo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, hot_diasemana VARCHAR(255) NOT NULL, hot_fecha DATE NOT NULL, hot_horaentrada TIME NOT NULL, hot_horasalida TIME NOT NULL, hot_tipojornada VARCHAR(255) NOT NULL, hot_eliminado BOOLEAN NOT NULL, colaborador_id INTEGER NOT NULL, CONSTRAINT FK_DA85417F1CB264E FOREIGN KEY (colaborador_id) REFERENCES colaborador (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DA85417F1CB264E ON horario_trabajo (colaborador_id)');
        $this->addSql('CREATE TABLE motivo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, mtv_nombre VARCHAR(255) NOT NULL, mtv_eliminado BOOLEAN NOT NULL, permiso_id INTEGER NOT NULL, CONSTRAINT FK_19F938666CEFAD37 FOREIGN KEY (permiso_id) REFERENCES permiso (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_19F938666CEFAD37 ON motivo (permiso_id)');
        $this->addSql('CREATE TABLE permiso (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pms_estado VARCHAR(255) NOT NULL, pms_eliminado BOOLEAN NOT NULL, colaborador_id INTEGER NOT NULL, CONSTRAINT FK_FD7AAB9EF1CB264E FOREIGN KEY (colaborador_id) REFERENCES colaborador (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FD7AAB9EF1CB264E ON permiso (colaborador_id)');
        $this->addSql('CREATE TABLE puesto (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pst_nombre VARCHAR(255) NOT NULL, pst_eliminado BOOLEAN NOT NULL, area_id INTEGER NOT NULL, CONSTRAINT FK_47C3D2DEBD0F409C FOREIGN KEY (area_id) REFERENCES area (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_47C3D2DEBD0F409C ON puesto (area_id)');
        $this->addSql('CREATE TABLE registro_cambios (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reg_tablaafectada VARCHAR(255) NOT NULL, reg_campoafectado VARCHAR(255) NOT NULL, reg_valoranterior VARCHAR(255) NOT NULL, reg_valornuevo VARCHAR(255) NOT NULL, reg_fecha DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE sede (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, sed_nombre VARCHAR(255) NOT NULL, sed_pais VARCHAR(255) NOT NULL, sed_direccion VARCHAR(255) NOT NULL, sed_ubicacion VARCHAR(255) NOT NULL, sed_eliminado BOOLEAN NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE area');
        $this->addSql('DROP TABLE asistencia');
        $this->addSql('DROP TABLE colaborador');
        $this->addSql('DROP TABLE configuracion_asistencia');
        $this->addSql('DROP TABLE empresa');
        $this->addSql('DROP TABLE grupo');
        $this->addSql('DROP TABLE horario_trabajo');
        $this->addSql('DROP TABLE motivo');
        $this->addSql('DROP TABLE permiso');
        $this->addSql('DROP TABLE puesto');
        $this->addSql('DROP TABLE registro_cambios');
        $this->addSql('DROP TABLE sede');
    }
}
