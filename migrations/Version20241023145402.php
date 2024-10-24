<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023145402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE area (id INT AUTO_INCREMENT NOT NULL, ara_nombre VARCHAR(255) NOT NULL, ara_eliminado TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE asistencia (id INT AUTO_INCREMENT NOT NULL, asi_fechaentrada DATETIME NOT NULL, asi_fechasalida DATETIME NOT NULL, asi_horaentrada TIME NOT NULL, asi_horasalida TIME NOT NULL, asi_fotoentrada VARCHAR(255) NOT NULL, asi_fotosalida VARCHAR(255) NOT NULL, asi_ubicacionentrada VARCHAR(255) NOT NULL, asi_ubicacionsalida VARCHAR(255) NOT NULL, asi_estadoentrada VARCHAR(255) NOT NULL, asi_estadosalida VARCHAR(255) NOT NULL, asi_notas VARCHAR(255) NOT NULL, asi_eliminado TINYINT(1) NOT NULL, colaborador_id INT NOT NULL, INDEX IDX_D8264A8DF1CB264E (colaborador_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE colaborador (id INT AUTO_INCREMENT NOT NULL, col_nombreusuario VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, col_nombres VARCHAR(255) NOT NULL, col_apellidos VARCHAR(255) NOT NULL, col_dninit VARCHAR(255) NOT NULL, col_fechainacimiento DATE NOT NULL, col_correoelecronico VARCHAR(255) NOT NULL, col_eliminado TINYINT(1) NOT NULL, grupo_id INT NOT NULL, INDEX IDX_D2F80BB39C833003 (grupo_id), UNIQUE INDEX UNIQ_IDENTIFIER_COL_NOMBREUSUARIO (col_nombreusuario), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE configuracion_asistencia (id INT AUTO_INCREMENT NOT NULL, cas_tiempo_falta_horas INT NOT NULL, cas_tolerancia_ingreso_minutos INT NOT NULL, cas_permitir_foto TINYINT(1) NOT NULL, cas_faltas_tardanzas TINYINT(1) NOT NULL, cas_permisos TINYINT(1) NOT NULL, cas_vacaciones TINYINT(1) NOT NULL, cas_marcacion TINYINT(1) NOT NULL, cas_modalidad VARCHAR(255) NOT NULL, cas_area TINYINT(1) NOT NULL, cas_puesto TINYINT(1) NOT NULL, cas_predhorario TINYINT(1) NOT NULL, cas_eliminado TINYINT(1) NOT NULL, cas_estado VARCHAR(255) NOT NULL, grupo_id INT NOT NULL, sede_id INT NOT NULL, puesto_id INT NOT NULL, INDEX IDX_FB7656889C833003 (grupo_id), INDEX IDX_FB765688E19F41BF (sede_id), INDEX IDX_FB7656885035E9DA (puesto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE empresa (id INT AUTO_INCREMENT NOT NULL, emp_ruc VARCHAR(255) NOT NULL, emp_nombreempresa VARCHAR(255) NOT NULL, emp_industria VARCHAR(255) NOT NULL, emp_telefono VARCHAR(255) NOT NULL, emp_cantidadempleados INT NOT NULL, emp_eliminado TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE grupo (id INT AUTO_INCREMENT NOT NULL, grp_nombre VARCHAR(255) NOT NULL, grp_descripcion VARCHAR(255) NOT NULL, grp_eliminado TINYINT(1) NOT NULL, empresa_id INT NOT NULL, INDEX IDX_8C0E9BD3521E1991 (empresa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE horario_trabajo (id INT AUTO_INCREMENT NOT NULL, hot_diasemana VARCHAR(255) NOT NULL, hot_fecha DATE NOT NULL, hot_horaentrada TIME NOT NULL, hot_horasalida TIME NOT NULL, hot_tipojornada VARCHAR(255) NOT NULL, hot_eliminado TINYINT(1) NOT NULL, colaborador_id INT NOT NULL, INDEX IDX_DA85417F1CB264E (colaborador_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE motivo (id INT AUTO_INCREMENT NOT NULL, mtv_nombre VARCHAR(255) NOT NULL, mtv_eliminado TINYINT(1) NOT NULL, permiso_id INT NOT NULL, INDEX IDX_19F938666CEFAD37 (permiso_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE permiso (id INT AUTO_INCREMENT NOT NULL, pms_estado VARCHAR(255) NOT NULL, pms_eliminado TINYINT(1) NOT NULL, colaborador_id INT NOT NULL, INDEX IDX_FD7AAB9EF1CB264E (colaborador_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE puesto (id INT AUTO_INCREMENT NOT NULL, pst_nombre VARCHAR(255) NOT NULL, pst_eliminado TINYINT(1) NOT NULL, area_id INT NOT NULL, INDEX IDX_47C3D2DEBD0F409C (area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE registro_cambios (id INT AUTO_INCREMENT NOT NULL, reg_tablaafectada VARCHAR(255) NOT NULL, reg_campoafectado VARCHAR(255) NOT NULL, reg_valoranterior VARCHAR(255) NOT NULL, reg_valornuevo VARCHAR(255) NOT NULL, reg_fecha DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sede (id INT AUTO_INCREMENT NOT NULL, sed_nombre VARCHAR(255) NOT NULL, sed_pais VARCHAR(255) NOT NULL, sed_direccion VARCHAR(255) NOT NULL, sed_ubicacion VARCHAR(255) NOT NULL, sed_eliminado TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE asistencia ADD CONSTRAINT FK_D8264A8DF1CB264E FOREIGN KEY (colaborador_id) REFERENCES colaborador (id)');
        $this->addSql('ALTER TABLE colaborador ADD CONSTRAINT FK_D2F80BB39C833003 FOREIGN KEY (grupo_id) REFERENCES grupo (id)');
        $this->addSql('ALTER TABLE configuracion_asistencia ADD CONSTRAINT FK_FB7656889C833003 FOREIGN KEY (grupo_id) REFERENCES grupo (id)');
        $this->addSql('ALTER TABLE configuracion_asistencia ADD CONSTRAINT FK_FB765688E19F41BF FOREIGN KEY (sede_id) REFERENCES sede (id)');
        $this->addSql('ALTER TABLE configuracion_asistencia ADD CONSTRAINT FK_FB7656885035E9DA FOREIGN KEY (puesto_id) REFERENCES puesto (id)');
        $this->addSql('ALTER TABLE grupo ADD CONSTRAINT FK_8C0E9BD3521E1991 FOREIGN KEY (empresa_id) REFERENCES empresa (id)');
        $this->addSql('ALTER TABLE horario_trabajo ADD CONSTRAINT FK_DA85417F1CB264E FOREIGN KEY (colaborador_id) REFERENCES colaborador (id)');
        $this->addSql('ALTER TABLE motivo ADD CONSTRAINT FK_19F938666CEFAD37 FOREIGN KEY (permiso_id) REFERENCES permiso (id)');
        $this->addSql('ALTER TABLE permiso ADD CONSTRAINT FK_FD7AAB9EF1CB264E FOREIGN KEY (colaborador_id) REFERENCES colaborador (id)');
        $this->addSql('ALTER TABLE puesto ADD CONSTRAINT FK_47C3D2DEBD0F409C FOREIGN KEY (area_id) REFERENCES area (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asistencia DROP FOREIGN KEY FK_D8264A8DF1CB264E');
        $this->addSql('ALTER TABLE colaborador DROP FOREIGN KEY FK_D2F80BB39C833003');
        $this->addSql('ALTER TABLE configuracion_asistencia DROP FOREIGN KEY FK_FB7656889C833003');
        $this->addSql('ALTER TABLE configuracion_asistencia DROP FOREIGN KEY FK_FB765688E19F41BF');
        $this->addSql('ALTER TABLE configuracion_asistencia DROP FOREIGN KEY FK_FB7656885035E9DA');
        $this->addSql('ALTER TABLE grupo DROP FOREIGN KEY FK_8C0E9BD3521E1991');
        $this->addSql('ALTER TABLE horario_trabajo DROP FOREIGN KEY FK_DA85417F1CB264E');
        $this->addSql('ALTER TABLE motivo DROP FOREIGN KEY FK_19F938666CEFAD37');
        $this->addSql('ALTER TABLE permiso DROP FOREIGN KEY FK_FD7AAB9EF1CB264E');
        $this->addSql('ALTER TABLE puesto DROP FOREIGN KEY FK_47C3D2DEBD0F409C');
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
