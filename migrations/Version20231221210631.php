<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231221210631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shipment (id INT AUTO_INCREMENT NOT NULL, origin_address_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', destination_address_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', storage_id INT DEFAULT NULL, channel_id INT DEFAULT NULL, carrier_id INT DEFAULT NULL, fulfilment_id INT DEFAULT NULL, code VARCHAR(32) NOT NULL, source_id VARCHAR(64) NOT NULL, id_on_sorce VARCHAR(255) DEFAULT NULL, status VARCHAR(32) NOT NULL, channel_order_id VARCHAR(32) DEFAULT NULL, channel_shipment_id VARCHAR(32) DEFAULT NULL, INDEX IDX_2CB20DC4C6CF538 (origin_address_id), INDEX IDX_2CB20DCA88E34C7 (destination_address_id), INDEX IDX_2CB20DC5CC5DB90 (storage_id), INDEX IDX_2CB20DC72F5A1AA (channel_id), INDEX IDX_2CB20DC21DFC797 (carrier_id), UNIQUE INDEX UNIQ_2CB20DC53F162F (fulfilment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipment_item (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, order_item_id INT DEFAULT NULL, storage_id INT DEFAULT NULL, shipment_id INT NOT NULL, fulfilment_id INT DEFAULT NULL, quantity INT NOT NULL, quantity_returned INT DEFAULT NULL, internal_order_item_id VARCHAR(32) DEFAULT NULL, INDEX IDX_1C573404584665A (product_id), INDEX IDX_1C57340E415FB15 (order_item_id), INDEX IDX_1C573405CC5DB90 (storage_id), INDEX IDX_1C573407BE036FC (shipment_id), UNIQUE INDEX UNIQ_1C5734053F162F (fulfilment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC4C6CF538 FOREIGN KEY (origin_address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DCA88E34C7 FOREIGN KEY (destination_address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC5CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC21DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id)');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC53F162F FOREIGN KEY (fulfilment_id) REFERENCES shipment_fulfilment (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C573404584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C57340E415FB15 FOREIGN KEY (order_item_id) REFERENCES order_item (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C573405CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C573407BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C5734053F162F FOREIGN KEY (fulfilment_id) REFERENCES shipment_fulfilment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC4C6CF538');
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DCA88E34C7');
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC5CC5DB90');
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC72F5A1AA');
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC21DFC797');
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC53F162F');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C573404584665A');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C57340E415FB15');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C573405CC5DB90');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C573407BE036FC');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C5734053F162F');
        $this->addSql('DROP TABLE shipment');
        $this->addSql('DROP TABLE shipment_item');
    }
}
