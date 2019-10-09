<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191008073457 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE floor ADD id_fridge_id INT NOT NULL');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E9491327E FOREIGN KEY (id_fridge_id) REFERENCES fridge (id)');
        $this->addSql('CREATE INDEX IDX_BE45D62E9491327E ON floor (id_fridge_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E9491327E');
        $this->addSql('DROP INDEX IDX_BE45D62E9491327E ON floor');
        $this->addSql('ALTER TABLE floor DROP id_fridge_id');
    }
}
