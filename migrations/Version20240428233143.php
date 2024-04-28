<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240428233143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__colaborador AS SELECT id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id FROM colaborador');
        $this->addSql('DROP TABLE colaborador');
        $this->addSql('CREATE TABLE colaborador (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, col_nombres VARCHAR(255) DEFAULT NULL, col_apellidos VARCHAR(255) DEFAULT NULL, col_dninit VARCHAR(255) DEFAULT NULL, col_fechanacimiento DATE DEFAULT NULL, col_area VARCHAR(255) DEFAULT NULL, col_correoelectronico VARCHAR(255) DEFAULT NULL, roles CLOB NOT NULL, col_nombreusuario VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, col_eliminado BOOLEAN DEFAULT NULL, col_empresa_id INTEGER DEFAULT NULL, col_grupo_id INTEGER DEFAULT NULL, col_puesto_id INTEGER DEFAULT NULL, CONSTRAINT FK_D2F80BB37F0EA5D2 FOREIGN KEY (col_empresa_id) REFERENCES empresa (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D2F80BB378A49F5A FOREIGN KEY (col_grupo_id) REFERENCES grupo (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D2F80BB342662725 FOREIGN KEY (col_puesto_id) REFERENCES puesto (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO colaborador (id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id) SELECT id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id FROM __temp__colaborador');
        $this->addSql('DROP TABLE __temp__colaborador');
        $this->addSql('CREATE INDEX IDX_D2F80BB378A49F5A ON colaborador (col_grupo_id)');
        $this->addSql('CREATE INDEX IDX_D2F80BB37F0EA5D2 ON colaborador (col_empresa_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2F80BB331B984AC ON colaborador (col_nombreusuario)');
        $this->addSql('CREATE INDEX IDX_D2F80BB342662725 ON colaborador (col_puesto_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__colaborador AS SELECT id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id FROM colaborador');
        $this->addSql('DROP TABLE colaborador');
        $this->addSql('CREATE TABLE colaborador (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, col_nombres VARCHAR(255) DEFAULT NULL, col_apellidos VARCHAR(255) DEFAULT NULL, col_dninit VARCHAR(255) DEFAULT NULL, col_fechanacimiento DATE DEFAULT NULL, col_area VARCHAR(255) DEFAULT NULL, col_correoelectronico VARCHAR(255) DEFAULT NULL, roles CLOB NOT NULL, col_nombreusuario VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, col_eliminado BOOLEAN DEFAULT NULL, col_empresa_id INTEGER DEFAULT NULL, col_grupo_id INTEGER DEFAULT NULL, CONSTRAINT FK_D2F80BB37F0EA5D2 FOREIGN KEY (col_empresa_id) REFERENCES empresa (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D2F80BB378A49F5A FOREIGN KEY (col_grupo_id) REFERENCES grupo (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO colaborador (id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id) SELECT id, col_nombres, col_apellidos, col_dninit, col_fechanacimiento, col_area, col_correoelectronico, roles, col_nombreusuario, password, col_eliminado, col_empresa_id, col_grupo_id FROM __temp__colaborador');
        $this->addSql('DROP TABLE __temp__colaborador');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2F80BB331B984AC ON colaborador (col_nombreusuario)');
        $this->addSql('CREATE INDEX IDX_D2F80BB37F0EA5D2 ON colaborador (col_empresa_id)');
        $this->addSql('CREATE INDEX IDX_D2F80BB378A49F5A ON colaborador (col_grupo_id)');
    }
}
