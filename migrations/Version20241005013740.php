<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241005013740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule_item ADD teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE schedule_item ADD CONSTRAINT FK_FF53054541807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FF53054541807E1D ON schedule_item (teacher_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE schedule_item DROP CONSTRAINT FK_FF53054541807E1D');
        $this->addSql('DROP INDEX IDX_FF53054541807E1D');
        $this->addSql('ALTER TABLE schedule_item DROP teacher_id');
    }
}
