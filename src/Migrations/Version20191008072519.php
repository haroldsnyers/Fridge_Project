<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191008072519 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY fk_floor_fridge');
        $this->addSql('DROP INDEX fk_floor_fridge_idx ON floor');
        $this->addSql('ALTER TABLE floor DROP fridge_id');
        $this->addSql('ALTER TABLE food DROP FOREIGN KEY fk_food_floor1');
        $this->addSql('DROP INDEX fk_food_floor1_idx ON food');
        $this->addSql('ALTER TABLE food DROP floor_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE floor ADD fridge_id INT NOT NULL');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT fk_floor_fridge FOREIGN KEY (fridge_id) REFERENCES fridge (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX fk_floor_fridge_idx ON floor (fridge_id)');
        $this->addSql('ALTER TABLE food ADD floor_id INT NOT NULL');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT fk_food_floor1 FOREIGN KEY (floor_id) REFERENCES floor (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX fk_food_floor1_idx ON food (floor_id)');
    }
}
