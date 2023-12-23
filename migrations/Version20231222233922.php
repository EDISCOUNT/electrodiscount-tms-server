<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231222233922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carrier ADD operator_user_id INT DEFAULT NULL, ADD email_address VARCHAR(128) DEFAULT NULL, ADD phone_number VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE carrier ADD CONSTRAINT FK_4739F11C3A2B0BF6 FOREIGN KEY (operator_user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4739F11C3A2B0BF6 ON carrier (operator_user_id)');
        $this->addSql('ALTER TABLE user ADD email VARCHAR(128) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carrier DROP FOREIGN KEY FK_4739F11C3A2B0BF6');
        $this->addSql('DROP INDEX UNIQ_4739F11C3A2B0BF6 ON carrier');
        $this->addSql('ALTER TABLE carrier DROP operator_user_id, DROP email_address, DROP phone_number');
        $this->addSql('ALTER TABLE `user` DROP email');
    }
}
