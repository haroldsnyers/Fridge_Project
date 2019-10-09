<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191008123831 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE food ADD id_floor_id INT NOT NULL');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT FK_D43829F7D59D9577 FOREIGN KEY (id_floor_id) REFERENCES floor (id)');
        $this->addSql('CREATE INDEX IDX_D43829F7D59D9577 ON food (id_floor_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE food DROP FOREIGN KEY FK_D43829F7D59D9577');
        $this->addSql('DROP INDEX IDX_D43829F7D59D9577 ON food');
        $this->addSql('ALTER TABLE food DROP id_floor_id');
    }
}
