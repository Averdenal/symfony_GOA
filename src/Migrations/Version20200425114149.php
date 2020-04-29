<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200425114149 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD firstname VARCHAR(255) DEFAULT NULL, ADD lastname VARCHAR(255) DEFAULT NULL, ADD pseudo VARCHAR(255) NOT NULL, ADD link_facebook VARCHAR(255) DEFAULT NULL, ADD link_twitter VARCHAR(255) DEFAULT NULL, ADD link_instagram VARCHAR(255) DEFAULT NULL, ADD link_youtube VARCHAR(255) DEFAULT NULL, ADD link_discord VARCHAR(255) DEFAULT NULL, ADD link_twitch VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP firstname, DROP lastname, DROP pseudo, DROP link_facebook, DROP link_twitter, DROP link_instagram, DROP link_youtube, DROP link_discord, DROP link_twitch');
    }
}
