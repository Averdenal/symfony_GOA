<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200503150545 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE friends DROP FOREIGN KEY FK_21EE70696BF700BD');
        $this->addSql('DROP TABLE friends_status');
        $this->addSql('DROP INDEX IDX_21EE70696BF700BD ON friends');
        $this->addSql('ALTER TABLE friends DROP status_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE friends_status (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE friends ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE friends ADD CONSTRAINT FK_21EE70696BF700BD FOREIGN KEY (status_id) REFERENCES friends_status (id)');
        $this->addSql('CREATE INDEX IDX_21EE70696BF700BD ON friends (status_id)');
    }
}
