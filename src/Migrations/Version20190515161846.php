<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190515161846 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE proformat (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, made_at DATETIME NOT NULL, motif VARCHAR(100) NOT NULL, cout INT NOT NULL, INDEX IDX_EA59AEE6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proformat_offre (id INT AUTO_INCREMENT NOT NULL, offre_id INT NOT NULL, proformat_id INT NOT NULL, nuite INT NOT NULL, cout INT NOT NULL, UNIQUE INDEX UNIQ_38C4A6C54CC8505A (offre_id), UNIQUE INDEX UNIQ_38C4A6C5E91D11FF (proformat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proformat_repas (id INT AUTO_INCREMENT NOT NULL, proformat_id INT NOT NULL, repas_id INT NOT NULL, quantite INT NOT NULL, cout INT NOT NULL, UNIQUE INDEX UNIQ_3F917119E91D11FF (proformat_id), UNIQUE INDEX UNIQ_3F9171191D236AAA (repas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proformat_salle (id INT AUTO_INCREMENT NOT NULL, salle_id INT NOT NULL, proformat_id INT NOT NULL, day INT NOT NULL, cout INT NOT NULL, UNIQUE INDEX UNIQ_D9D55EF6DC304035 (salle_id), UNIQUE INDEX UNIQ_D9D55EF6E91D11FF (proformat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proformat ADD CONSTRAINT FK_EA59AEE6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE proformat_offre ADD CONSTRAINT FK_38C4A6C54CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)');
        $this->addSql('ALTER TABLE proformat_offre ADD CONSTRAINT FK_38C4A6C5E91D11FF FOREIGN KEY (proformat_id) REFERENCES proformat (id)');
        $this->addSql('ALTER TABLE proformat_repas ADD CONSTRAINT FK_3F917119E91D11FF FOREIGN KEY (proformat_id) REFERENCES proformat (id)');
        $this->addSql('ALTER TABLE proformat_repas ADD CONSTRAINT FK_3F9171191D236AAA FOREIGN KEY (repas_id) REFERENCES repas (id)');
        $this->addSql('ALTER TABLE proformat_salle ADD CONSTRAINT FK_D9D55EF6DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE proformat_salle ADD CONSTRAINT FK_D9D55EF6E91D11FF FOREIGN KEY (proformat_id) REFERENCES proformat (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE proformat_offre DROP FOREIGN KEY FK_38C4A6C5E91D11FF');
        $this->addSql('ALTER TABLE proformat_repas DROP FOREIGN KEY FK_3F917119E91D11FF');
        $this->addSql('ALTER TABLE proformat_salle DROP FOREIGN KEY FK_D9D55EF6E91D11FF');
        $this->addSql('DROP TABLE proformat');
        $this->addSql('DROP TABLE proformat_offre');
        $this->addSql('DROP TABLE proformat_repas');
        $this->addSql('DROP TABLE proformat_salle');
        $this->addSql('DROP INDEX UNIQ_49E7720DB83297E7 ON identification');
        $this->addSql('ALTER TABLE identification CHANGE reservation_id reservation_id INT DEFAULT NULL, CHANGE cout cout DOUBLE PRECISION DEFAULT \'0\', CHANGE avance avance INT DEFAULT 0 NOT NULL, CHANGE etat etat VARCHAR(20) DEFAULT \'en cours\' COLLATE utf8mb4_unicode_ci, CHANGE cout_extra cout_extra INT DEFAULT 0, CHANGE remise remise INT DEFAULT 0, CHANGE nombre_nuite nombre_nuite INT DEFAULT 0');
        $this->addSql('CREATE INDEX UNIQ_49E7720DB83297E7 ON identification (reservation_id)');
        $this->addSql('DROP INDEX UNIQ_42C849554CC8505A ON reservation');
        $this->addSql('DROP INDEX UNIQ_42C8495519EB6921 ON reservation');
        $this->addSql('CREATE INDEX UNIQ_42C849554CC8505A ON reservation (offre_id)');
        $this->addSql('CREATE INDEX UNIQ_42C8495519EB6921 ON reservation (client_id)');
    }
}
