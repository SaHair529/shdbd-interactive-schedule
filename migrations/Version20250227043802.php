<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227043802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subject_user (subject_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(subject_id, user_id))');
        $this->addSql('CREATE INDEX IDX_1F59529223EDC87 ON subject_user (subject_id)');
        $this->addSql('CREATE INDEX IDX_1F595292A76ED395 ON subject_user (user_id)');
        $this->addSql('ALTER TABLE subject_user ADD CONSTRAINT FK_1F59529223EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_user ADD CONSTRAINT FK_1F595292A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE schedule_item DROP CONSTRAINT FK_FF53054523EDC87');
        $this->addSql('ALTER TABLE schedule_item ADD CONSTRAINT FK_FF53054523EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subject_user DROP CONSTRAINT FK_1F59529223EDC87');
        $this->addSql('ALTER TABLE subject_user DROP CONSTRAINT FK_1F595292A76ED395');
        $this->addSql('DROP TABLE subject_user');
        $this->addSql('ALTER TABLE schedule_item DROP CONSTRAINT fk_ff53054523edc87');
        $this->addSql('ALTER TABLE schedule_item ADD CONSTRAINT fk_ff53054523edc87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
