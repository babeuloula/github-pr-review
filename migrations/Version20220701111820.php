<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220701111820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE TABLE user (
    id INT UNSIGNED AUTO_INCREMENT NOT NULL,
    username VARCHAR(255) NOT NULL,
    roles JSON NOT NULL,
    token VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    enabled TINYINT(1) DEFAULT 1 NOT NULL,
    UNIQUE INDEX UNIQ_8D93D649F85E0677 (username),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user');
    }
}
