<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231221134011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shipment_fulfilment (id INT AUTO_INCREMENT NOT NULL, method VARCHAR(8) DEFAULT NULL, distribution_party VARCHAR(64) DEFAULT NULL, latest_delivery_date DATE DEFAULT NULL, exact_delivery_date DATE DEFAULT NULL, expiry_date DATE NOT NULL, time_frame_type VARCHAR(16) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_item ADD fulfilment_id INT DEFAULT NULL, ADD channel_order_item_id VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F0953F162F FOREIGN KEY (fulfilment_id) REFERENCES shipment_fulfilment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52EA1F0953F162F ON order_item (fulfilment_id)');
        $this->addSql('ALTER TABLE shipment ADD fulfilment_id INT DEFAULT NULL, ADD channel_shipment_id VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC53F162F FOREIGN KEY (fulfilment_id) REFERENCES shipment_fulfilment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CB20DC53F162F ON shipment (fulfilment_id)');
        $this->addSql('ALTER TABLE shipment_item ADD fulfilment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C5734053F162F FOREIGN KEY (fulfilment_id) REFERENCES shipment_fulfilment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1C5734053F162F ON shipment_item (fulfilment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F0953F162F');
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC53F162F');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C5734053F162F');
        $this->addSql('DROP TABLE shipment_fulfilment');
        $this->addSql('DROP INDEX UNIQ_52EA1F0953F162F ON order_item');
        $this->addSql('ALTER TABLE order_item DROP fulfilment_id, DROP channel_order_item_id');
        $this->addSql('DROP INDEX UNIQ_2CB20DC53F162F ON shipment');
        $this->addSql('ALTER TABLE shipment DROP fulfilment_id, DROP channel_shipment_id');
        $this->addSql('DROP INDEX UNIQ_1C5734053F162F ON shipment_item');
        $this->addSql('ALTER TABLE shipment_item DROP fulfilment_id');
    }
}
