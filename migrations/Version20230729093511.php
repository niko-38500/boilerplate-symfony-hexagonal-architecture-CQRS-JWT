<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230729093511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE temporary_token (token VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(token))');
        $this->addSql('ALTER TABLE user ADD COLUMN email_verification_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE temporary_token');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT uuid, username, email, password, is_account_validated FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (uuid VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_account_validated BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('INSERT INTO user (uuid, username, email, password, is_account_validated) SELECT uuid, username, email, password, is_account_validated FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }
}
