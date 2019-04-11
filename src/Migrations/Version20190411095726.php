<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190411095726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client ADD type VARCHAR(20) DEFAULT NULL, ADD remise INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_42C8495519EB6921 ON reservation');
        $this->addSql('DROP INDEX UNIQ_42C849554CC8505A ON reservation');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C8495519EB6921 ON reservation (client_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C849554CC8505A ON reservation (offre_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client DROP type, DROP remise');
        $this->addSql('ALTER TABLE identification CHANGE reservation_id reservation_id INT DEFAULT NULL, CHANGE cout cout DOUBLE PRECISION DEFAULT \'0\', CHANGE etat etat VARCHAR(20) DEFAULT \'en cours\' COLLATE utf8mb4_unicode_ci, CHANGE cout_extra cout_extra INT DEFAULT 0, CHANGE remise remise INT DEFAULT 0, CHANGE nombre_nuite nombre_nuite INT DEFAULT 0');
        $this->addSql('DROP INDEX UNIQ_42C849554CC8505A ON reservation');
        $this->addSql('DROP INDEX UNIQ_42C8495519EB6921 ON reservation');
        $this->addSql('CREATE INDEX UNIQ_42C849554CC8505A ON reservation (offre_id)');
        $this->addSql('CREATE INDEX UNIQ_42C8495519EB6921 ON reservation (client_id)');
    }
}
