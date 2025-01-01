<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250101222352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change roles column type from json to jsonb';
    }

    public function up(Schema $schema): void
    {
        // Изменение типа столбца roles с json на jsonb
        $this->addSql('ALTER TABLE "user" ALTER COLUMN roles TYPE jsonb USING roles::jsonb');
    }

    public function down(Schema $schema): void
    {
        // Возврат изменения: изменение типа столбца roles с jsonb на json
        $this->addSql('ALTER TABLE "user" ALTER COLUMN roles TYPE json USING roles::json');
    }
}
