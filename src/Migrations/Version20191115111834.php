<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191115111834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Use DoctrineType for color and use mode.';
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
ALTER TABLE configuration
CHANGE mode mode VARCHAR(25) NOT NULL COMMENT '(DC2Type:use_mode)',
CHANGE branch_default_color branch_default_color VARCHAR(25) NOT NULL COMMENT '(DC2Type:color)'
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
ALTER TABLE configuration
CHANGE mode mode VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
CHANGE branch_default_color branch_default_color VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`
SQL
        );
    }
}
