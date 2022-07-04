<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220701121736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create configuration table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE TABLE configuration (
    id INT UNSIGNED AUTO_INCREMENT NOT NULL,
    repositories JSON NOT NULL,
    mode ENUM('filter', 'label') NOT NULL COMMENT '(DC2Type:enum_use_mode)',
    labels_review_needed JSON NOT NULL,
    labels_changes_requested JSON NOT NULL,
    labels_accepted JSON NOT NULL,
    labels_wip JSON NOT NULL,
    branches_colors JSON NOT NULL,
    branch_default_color ENUM('primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark') NOT NULL COMMENT '(DC2Type:enum_color)',
    filters JSON NOT NULL,
    notifications_exclude_reasons JSON NOT NULL,
    notifications_exclude_reasons_other_repos JSON NOT NULL,
    enabled_dark_theme TINYINT(1) DEFAULT 0 NOT NULL,
    reload_on_focus TINYINT(1) DEFAULT 0 NOT NULL,
    reload_every INT UNSIGNED DEFAULT 60 NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
        $this->addSql('ALTER TABLE user ADD configuration_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64973F32DD8 FOREIGN KEY (configuration_id) REFERENCES configuration (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64973F32DD8 ON user (configuration_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64973F32DD8');
        $this->addSql('DROP TABLE configuration');
        $this->addSql('DROP INDEX UNIQ_8D93D64973F32DD8 ON user');
        $this->addSql('ALTER TABLE user DROP configuration_id');
    }
}
