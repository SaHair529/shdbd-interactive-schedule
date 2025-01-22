<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250122131825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify foreign key constraint on schedule_item for subject to include ON DELETE CASCADE';
    }

    public function up(Schema $schema): void
    {
        // Удаляем существующее ограничение внешнего ключа
        $this->addSql('ALTER TABLE schedule_item DROP CONSTRAINT fk_ff53054523edc87');

        // Добавляем новое ограничение с каскадным удалением
        $this->addSql('ALTER TABLE schedule_item ADD CONSTRAINT fk_ff53054523edc87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Откат изменений: удаляем новое ограничение и восстанавливаем старое (если нужно)
        $this->addSql('ALTER TABLE schedule_item DROP CONSTRAINT fk_ff53054523edc87');
        // Здесь можно добавить восстановление старого ограничения, если оно было.
    }
}
