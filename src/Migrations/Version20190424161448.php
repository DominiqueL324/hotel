<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190424161448 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, salle_id INT NOT NULL, client_id INT NOT NULL, user_id INT NOT NULL, begin_at DATETIME NOT NULL, end_at DATETIME NOT NULL, remise INT DEFAULT NULL, cout_total INT NOT NULL, etat VARCHAR(10) NOT NULL, motif VARCHAR(50) DEFAULT NULL, caution INT DEFAULT NULL, INDEX IDX_5E9E89CBDC304035 (salle_id), INDEX IDX_5E9E89CB19EB6921 (client_id), INDEX IDX_5E9E89CBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, type_salle VARCHAR(50) DEFAULT NULL, places INT NOT NULL, prix INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX UNIQ_49E7720DB83297E7 ON identification');
        $this->addSql('DROP INDEX UNIQ_42C849554CC8505A ON reservation');
        $this->addSql('DROP INDEX UNIQ_42C8495519EB6921 ON reservation');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C849554CC8505A ON reservation (offre_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C8495519EB6921 ON reservation (client_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CBDC304035');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP INDEX UNIQ_49E7720DB83297E7 ON identification');
        $this->addSql('ALTER TABLE identification CHANGE reservation_id reservation_id INT DEFAULT NULL, CHANGE cout cout DOUBLE PRECISION DEFAULT \'0\', CHANGE etat etat VARCHAR(20) DEFAULT \'en cours\' COLLATE utf8mb4_unicode_ci, CHANGE cout_extra cout_extra INT DEFAULT 0, CHANGE remise remise INT DEFAULT 0, CHANGE nombre_nuite nombre_nuite INT DEFAULT 0');
        $this->addSql('CREATE INDEX UNIQ_49E7720DB83297E7 ON identification (reservation_id)');
        $this->addSql('DROP INDEX UNIQ_42C849554CC8505A ON reservation');
        $this->addSql('DROP INDEX UNIQ_42C8495519EB6921 ON reservation');
        $this->addSql('CREATE INDEX UNIQ_42C849554CC8505A ON reservation (offre_id)');
        $this->addSql('CREATE INDEX UNIQ_42C8495519EB6921 ON reservation (client_id)');
    }
}
