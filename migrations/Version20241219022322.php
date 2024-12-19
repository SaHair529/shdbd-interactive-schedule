<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219022322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "group_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "group" (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE schedule ADD groupp_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB1D829221 FOREIGN KEY (groupp_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5A3811FB1D829221 ON schedule (groupp_id)');
        $this->addSql('ALTER TABLE "user" ADD groupp_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6491D829221 FOREIGN KEY (groupp_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D6491D829221 ON "user" (groupp_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE schedule DROP CONSTRAINT FK_5A3811FB1D829221');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6491D829221');
        $this->addSql('DROP SEQUENCE "group_id_seq" CASCADE');
        $this->addSql('DROP TABLE "group"');
        $this->addSql('DROP INDEX IDX_8D93D6491D829221');
        $this->addSql('ALTER TABLE "user" DROP groupp_id');
        $this->addSql('DROP INDEX IDX_5A3811FB1D829221');
        $this->addSql('ALTER TABLE schedule DROP groupp_id');
    }
}
