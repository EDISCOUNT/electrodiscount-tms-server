<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231225191741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shipment_dimension (id INT AUTO_INCREMENT NOT NULL, length INT NOT NULL, width INT NOT NULL, height INT NOT NULL, unit VARCHAR(32) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD province_code VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD channel_order_created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE order_item ADD status VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE shipment ADD dimension_id INT DEFAULT NULL, ADD net_weight INT DEFAULT NULL, ADD volumetric_weight INT DEFAULT NULL, ADD cod_amount INT DEFAULT NULL, ADD cod_currency VARCHAR(3) DEFAULT NULL, ADD booked_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC277428AD FOREIGN KEY (dimension_id) REFERENCES shipment_dimension (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CB20DC277428AD ON shipment (dimension_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC277428AD');
        $this->addSql('DROP TABLE shipment_dimension');
        $this->addSql('ALTER TABLE address DROP province_code');
        $this->addSql('ALTER TABLE `order` DROP channel_order_created_at');
        $this->addSql('ALTER TABLE order_item DROP status');
        $this->addSql('DROP INDEX UNIQ_2CB20DC277428AD ON shipment');
        $this->addSql('ALTER TABLE shipment DROP dimension_id, DROP net_weight, DROP volumetric_weight, DROP cod_amount, DROP cod_currency, DROP booked_at');
    }
}
