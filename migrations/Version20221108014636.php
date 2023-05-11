<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221108014636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE eshop_config (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', streamer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', currency_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', delivery_price INT NOT NULL, UNIQUE INDEX UNIQ_F18B656525F432AD (streamer_id), INDEX IDX_F18B656538248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE eshop_config ADD CONSTRAINT FK_F18B656525F432AD FOREIGN KEY (streamer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE eshop_config ADD CONSTRAINT FK_F18B656538248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE cart ADD streamer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B725F432AD FOREIGN KEY (streamer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BA388B725F432AD ON cart (streamer_id)');
        $this->addSql('ALTER TABLE `order` ADD currency_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD delivery_price INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939838248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('CREATE INDEX IDX_F529939838248176 ON `order` (currency_id)');
        $this->addSql('ALTER TABLE product ADD date_from DATETIME DEFAULT NULL, ADD date_to DATETIME DEFAULT NULL, ADD active TINYINT(1) NOT NULL, ADD total_limit INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE eshop_config DROP FOREIGN KEY FK_F18B656525F432AD');
        $this->addSql('ALTER TABLE eshop_config DROP FOREIGN KEY FK_F18B656538248176');
        $this->addSql('DROP TABLE eshop_config');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939838248176');
        $this->addSql('DROP INDEX IDX_F529939838248176 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP currency_id, DROP delivery_price');
        $this->addSql('ALTER TABLE product DROP date_from, DROP date_to, DROP active, DROP total_limit');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B725F432AD');
        $this->addSql('DROP INDEX IDX_BA388B725F432AD ON cart');
        $this->addSql('ALTER TABLE cart DROP streamer_id');
    }
}
