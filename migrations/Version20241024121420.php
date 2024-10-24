<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241024121420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permiso ADD motivo_id INT NOT NULL');
        $this->addSql('ALTER TABLE permiso ADD CONSTRAINT FK_FD7AAB9EF9E584F8 FOREIGN KEY (motivo_id) REFERENCES motivo (id)');
        $this->addSql('CREATE INDEX IDX_FD7AAB9EF9E584F8 ON permiso (motivo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permiso DROP FOREIGN KEY FK_FD7AAB9EF9E584F8');
        $this->addSql('DROP INDEX IDX_FD7AAB9EF9E584F8 ON permiso');
        $this->addSql('ALTER TABLE permiso DROP motivo_id');
    }
}
