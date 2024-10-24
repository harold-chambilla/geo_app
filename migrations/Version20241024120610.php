<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241024120610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motivo DROP FOREIGN KEY FK_19F938666CEFAD37');
        $this->addSql('DROP INDEX IDX_19F938666CEFAD37 ON motivo');
        $this->addSql('ALTER TABLE motivo DROP permiso_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motivo ADD permiso_id INT NOT NULL');
        $this->addSql('ALTER TABLE motivo ADD CONSTRAINT FK_19F938666CEFAD37 FOREIGN KEY (permiso_id) REFERENCES permiso (id)');
        $this->addSql('CREATE INDEX IDX_19F938666CEFAD37 ON motivo (permiso_id)');
    }
}
