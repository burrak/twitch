<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221031011517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE eshop_config (id INT AUTO_INCREMENT NOT NULL, streamer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', currency_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', delivery_price INT NOT NULL, UNIQUE INDEX UNIQ_F18B656525F432AD (streamer_id), INDEX IDX_F18B656538248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE eshop_config ADD CONSTRAINT FK_F18B656525F432AD FOREIGN KEY (streamer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE eshop_config ADD CONSTRAINT FK_F18B656538248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE eshop_config DROP FOREIGN KEY FK_F18B656525F432AD');
        $this->addSql('ALTER TABLE eshop_config DROP FOREIGN KEY FK_F18B656538248176');
        $this->addSql('DROP TABLE eshop_config');
    }
}
