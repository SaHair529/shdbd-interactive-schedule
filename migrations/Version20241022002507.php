<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022002507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE schedule_event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE schedule_event (id INT NOT NULL, student_id INT NOT NULL, schedule_item_id INT NOT NULL, reason VARCHAR(255) DEFAULT NULL, type INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C7F7CAFBCB944F1A ON schedule_event (student_id)');
        $this->addSql('CREATE INDEX IDX_C7F7CAFB9F057EEF ON schedule_event (schedule_item_id)');
        $this->addSql('ALTER TABLE schedule_event ADD CONSTRAINT FK_C7F7CAFBCB944F1A FOREIGN KEY (student_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE schedule_event ADD CONSTRAINT FK_C7F7CAFB9F057EEF FOREIGN KEY (schedule_item_id) REFERENCES schedule_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE schedule_event_id_seq CASCADE');
        $this->addSql('ALTER TABLE schedule_event DROP CONSTRAINT FK_C7F7CAFBCB944F1A');
        $this->addSql('ALTER TABLE schedule_event DROP CONSTRAINT FK_C7F7CAFB9F057EEF');
        $this->addSql('DROP TABLE schedule_event');
    }
}
