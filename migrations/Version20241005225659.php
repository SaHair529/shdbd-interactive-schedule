<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241005225659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE schedule_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE schedule (id INT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE schedule_item ADD schedule_id INT NOT NULL');
        $this->addSql('ALTER TABLE schedule_item ADD CONSTRAINT FK_FF530545A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FF530545A40BC2D5 ON schedule_item (schedule_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE schedule_item DROP CONSTRAINT FK_FF530545A40BC2D5');
        $this->addSql('DROP SEQUENCE schedule_id_seq CASCADE');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP INDEX IDX_FF530545A40BC2D5');
        $this->addSql('ALTER TABLE schedule_item DROP schedule_id');
    }
}
