<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191114110124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update user table, rename some fields.';
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
ALTER TABLE user
ADD token VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
ADD name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
ADD nickname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
DROP github_token,
DROP firstname,
DROP lastname
SQL
        );

        $this->addSql(
            <<<SQL
CREATE UNIQUE INDEX UNIQ_8D93D649A188FE64 ON user (nickname)
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
ALTER TABLE user 
ADD github_token VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
ADD firstname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
ADD lastname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
DROP token, DROP name, DROP nickname
SQL
        );

        $this->addSql(
            <<<SQL
DROP INDEX UNIQ_8D93D649A188FE64 ON user
SQL
        );
    }
}
