<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200503130130 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE friends ADD user1_id INT NOT NULL, ADD user2_id INT NOT NULL');
        $this->addSql('ALTER TABLE friends ADD CONSTRAINT FK_21EE706956AE248B FOREIGN KEY (user1_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friends ADD CONSTRAINT FK_21EE7069441B8B65 FOREIGN KEY (user2_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_21EE706956AE248B ON friends (user1_id)');
        $this->addSql('CREATE INDEX IDX_21EE7069441B8B65 ON friends (user2_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE friends DROP FOREIGN KEY FK_21EE706956AE248B');
        $this->addSql('ALTER TABLE friends DROP FOREIGN KEY FK_21EE7069441B8B65');
        $this->addSql('DROP INDEX IDX_21EE706956AE248B ON friends');
        $this->addSql('DROP INDEX IDX_21EE7069441B8B65 ON friends');
        $this->addSql('ALTER TABLE friends DROP user1_id, DROP user2_id');
    }
}
