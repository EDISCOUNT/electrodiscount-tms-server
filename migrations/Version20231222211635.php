<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231222211635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shipment_additional_service (shipment_id INT NOT NULL, additional_service_id INT NOT NULL, INDEX IDX_ECE536A87BE036FC (shipment_id), INDEX IDX_ECE536A8F8E98E09 (additional_service_id), PRIMARY KEY(shipment_id, additional_service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipment_shipment_event (shipment_id INT NOT NULL, shipment_event_id INT NOT NULL, INDEX IDX_3580A5B87BE036FC (shipment_id), INDEX IDX_3580A5B8D7A017EA (shipment_event_id), PRIMARY KEY(shipment_id, shipment_event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipment_event (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(128) NOT NULL, type VARCHAR(32) NOT NULL, title VARCHAR(128) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, description VARCHAR(1000) DEFAULT NULL, metadata JSON DEFAULT NULL, event_occured_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shipment_additional_service ADD CONSTRAINT FK_ECE536A87BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shipment_additional_service ADD CONSTRAINT FK_ECE536A8F8E98E09 FOREIGN KEY (additional_service_id) REFERENCES additional_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shipment_shipment_event ADD CONSTRAINT FK_3580A5B87BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shipment_shipment_event ADD CONSTRAINT FK_3580A5B8D7A017EA FOREIGN KEY (shipment_event_id) REFERENCES shipment_event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment_additional_service DROP FOREIGN KEY FK_ECE536A87BE036FC');
        $this->addSql('ALTER TABLE shipment_additional_service DROP FOREIGN KEY FK_ECE536A8F8E98E09');
        $this->addSql('ALTER TABLE shipment_shipment_event DROP FOREIGN KEY FK_3580A5B87BE036FC');
        $this->addSql('ALTER TABLE shipment_shipment_event DROP FOREIGN KEY FK_3580A5B8D7A017EA');
        $this->addSql('DROP TABLE shipment_additional_service');
        $this->addSql('DROP TABLE shipment_shipment_event');
        $this->addSql('DROP TABLE shipment_event');
    }
}
