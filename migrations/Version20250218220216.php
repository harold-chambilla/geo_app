<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218220216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asistencia CHANGE asi_fechaentrada asi_fechaentrada DATETIME DEFAULT NULL, CHANGE asi_horaentrada asi_horaentrada TIME DEFAULT NULL, CHANGE asi_fotoentrada asi_fotoentrada VARCHAR(255) DEFAULT NULL, CHANGE asi_ubicacionentrada asi_ubicacionentrada VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asistencia CHANGE asi_fechaentrada asi_fechaentrada DATETIME NOT NULL, CHANGE asi_horaentrada asi_horaentrada TIME NOT NULL, CHANGE asi_fotoentrada asi_fotoentrada VARCHAR(255) NOT NULL, CHANGE asi_ubicacionentrada asi_ubicacionentrada VARCHAR(255) NOT NULL');
    }
}
