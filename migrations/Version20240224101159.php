<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240224101159 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE messenger_dlq (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F8B1DD95FB7336F0 ON messenger_dlq (queue_name)');
        $this->addSql('CREATE INDEX IDX_F8B1DD95E3BD61CE ON messenger_dlq (available_at)');
        $this->addSql('CREATE INDEX IDX_F8B1DD9516BA31DB ON messenger_dlq (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_dlq.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_dlq.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_dlq.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_dlq() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_dlq\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_dlq;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_dlq FOR EACH ROW EXECUTE PROCEDURE notify_messenger_dlq();');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE messenger_dlq');
    }
}
