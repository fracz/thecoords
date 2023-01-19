<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230119165004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial DB structure.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', short_unique_id CHAR(32) NOT NULL, access_token VARCHAR(255) NOT NULL, refresh_token VARCHAR(255) DEFAULT NULL, token_expiration_time DATETIME DEFAULT NULL COMMENT \'(DC2Type:utcdatetime)\', regulations_accepted TINYINT(1) DEFAULT 0 NOT NULL, timezone VARCHAR(50) DEFAULT NULL, locale VARCHAR(5) DEFAULT NULL, settings VARCHAR(1024) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E99DAF5974 (short_unique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE users');
    }
}
