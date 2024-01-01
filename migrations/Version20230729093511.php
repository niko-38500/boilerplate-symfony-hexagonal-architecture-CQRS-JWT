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
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE temporary_token (token VARCHAR(255) NOT NULL, expires_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(token))');
        $this->addSql('COMMENT ON COLUMN temporary_token.expires_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE temporary_token');
    }
}
