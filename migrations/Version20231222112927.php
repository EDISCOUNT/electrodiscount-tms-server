<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231222112927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2F98E4777153098 ON channel (code)');
        $this->addSql('ALTER TABLE `order` ADD channel_order_number VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE shipment_item ADD name VARCHAR(64) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_A2F98E4777153098 ON channel');
        $this->addSql('ALTER TABLE `order` DROP channel_order_number');
        $this->addSql('ALTER TABLE order_item DROP name');
        $this->addSql('ALTER TABLE shipment_item DROP name');
    }
}
