<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191113153830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for \'user\' and \'configuration\' table.';
    }

    // phpcs:ignore
    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            <<<SQL
CREATE TABLE configuration (
    id INT AUTO_INCREMENT NOT NULL,
    user_id INT DEFAULT NULL,
    repositories LONGTEXT NOT NULL COMMENT '(DC2Type:array)',
    mode VARCHAR(25) NOT NULL,
    labels_review_needed LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
    labels_changes_requested LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
    labels_accepted LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
    labels_wip LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
    branchs_colors LONGTEXT NOT NULL COMMENT '(DC2Type:array)',
    branch_default_color VARCHAR(25) NOT NULL,
    filters LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
    notifications_exclude_reasons LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
    notifications_exclude_reasons_other_repos LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
    enabled_dark_theme TINYINT(1) NOT NULL,
    reload_on_focus TINYINT(1) NOT NULL,
    reload_every INT UNSIGNED NOT NULL,
    UNIQUE INDEX UNIQ_A5E2A5D7A76ED395 (user_id),
    PRIMARY KEY(id)
)
DEFAULT CHARACTER SET utf8mb4
COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
        $this->addSql(
            <<<SQL
CREATE TABLE user (
    id INT AUTO_INCREMENT NOT NULL,
    github_token VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    enabled TINYINT(1) NOT NULL,
    PRIMARY KEY(id)
)
DEFAULT CHARACTER SET utf8mb4
COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
        $this->addSql(
            <<<SQL
ALTER TABLE configuration
ADD CONSTRAINT FK_A5E2A5D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
SQL
        );
    }

    // phpcs:ignore
    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            <<<SQL
ALTER TABLE configuration DROP FOREIGN KEY FK_A5E2A5D7A76ED395
SQL
        );
        $this->addSql(
            <<<SQL
DROP TABLE configuration
SQL
        );
        $this->addSql(
            <<<SQL
DROP TABLE user
SQL
        );
    }
}
