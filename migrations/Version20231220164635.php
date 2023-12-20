<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220164635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_additional_service (order_id INT NOT NULL, additional_service_id INT NOT NULL, INDEX IDX_1CBAA2108D9F6D38 (order_id), INDEX IDX_1CBAA210F8E98E09 (additional_service_id), PRIMARY KEY(order_id, additional_service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage (id INT AUTO_INCREMENT NOT NULL, address_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', name VARCHAR(64) NOT NULL, code VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_547A1B34F5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_additional_service ADD CONSTRAINT FK_1CBAA2108D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_additional_service ADD CONSTRAINT FK_1CBAA210F8E98E09 FOREIGN KEY (additional_service_id) REFERENCES additional_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE `order` ADD channel_id INT DEFAULT NULL, ADD channel_order_id VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939872F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('CREATE INDEX IDX_F529939872F5A1AA ON `order` (channel_id)');
        $this->addSql('ALTER TABLE shipment ADD storage_id INT DEFAULT NULL, ADD channel_id INT DEFAULT NULL, ADD channel_order_id VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC5CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('CREATE INDEX IDX_2CB20DC5CC5DB90 ON shipment (storage_id)');
        $this->addSql('CREATE INDEX IDX_2CB20DC72F5A1AA ON shipment (channel_id)');
        $this->addSql('ALTER TABLE shipment_item ADD product_id INT DEFAULT NULL, ADD order_item_id INT DEFAULT NULL, ADD storage_id INT DEFAULT NULL, ADD shipment_id INT NOT NULL, ADD quantity INT NOT NULL, ADD quantity_returned INT DEFAULT NULL, ADD internal_order_item_id VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C573404584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C57340E415FB15 FOREIGN KEY (order_item_id) REFERENCES order_item (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C573405CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C573407BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id)');
        $this->addSql('CREATE INDEX IDX_1C573404584665A ON shipment_item (product_id)');
        $this->addSql('CREATE INDEX IDX_1C57340E415FB15 ON shipment_item (order_item_id)');
        $this->addSql('CREATE INDEX IDX_1C573405CC5DB90 ON shipment_item (storage_id)');
        $this->addSql('CREATE INDEX IDX_1C573407BE036FC ON shipment_item (shipment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC5CC5DB90');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C573405CC5DB90');
        $this->addSql('ALTER TABLE order_additional_service DROP FOREIGN KEY FK_1CBAA2108D9F6D38');
        $this->addSql('ALTER TABLE order_additional_service DROP FOREIGN KEY FK_1CBAA210F8E98E09');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34F5B7AF75');
        $this->addSql('DROP TABLE order_additional_service');
        $this->addSql('DROP TABLE storage');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939872F5A1AA');
        $this->addSql('DROP INDEX IDX_F529939872F5A1AA ON `order`');
        $this->addSql('ALTER TABLE `order` DROP channel_id, DROP channel_order_id');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C573404584665A');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C57340E415FB15');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C573407BE036FC');
        $this->addSql('DROP INDEX IDX_1C573404584665A ON shipment_item');
        $this->addSql('DROP INDEX IDX_1C57340E415FB15 ON shipment_item');
        $this->addSql('DROP INDEX IDX_1C573405CC5DB90 ON shipment_item');
        $this->addSql('DROP INDEX IDX_1C573407BE036FC ON shipment_item');
        $this->addSql('ALTER TABLE shipment_item DROP product_id, DROP order_item_id, DROP storage_id, DROP shipment_id, DROP quantity, DROP quantity_returned, DROP internal_order_item_id');
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC72F5A1AA');
        $this->addSql('DROP INDEX IDX_2CB20DC5CC5DB90 ON shipment');
        $this->addSql('DROP INDEX IDX_2CB20DC72F5A1AA ON shipment');
        $this->addSql('ALTER TABLE shipment DROP storage_id, DROP channel_id, DROP channel_order_id');
    }
}
