<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220215949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment ADD carrier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC21DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id)');
        $this->addSql('CREATE INDEX IDX_2CB20DC21DFC797 ON shipment (carrier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC21DFC797');
        $this->addSql('DROP INDEX IDX_2CB20DC21DFC797 ON shipment');
        $this->addSql('ALTER TABLE shipment DROP carrier_id');
    }
}
