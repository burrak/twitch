<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221028234822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', streamer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', quantity INT NOT NULL, INDEX IDX_BA388B7A76ED395 (user_id), INDEX IDX_BA388B74584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', broadcaster_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', webhook_event_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', subscriber INT NOT NULL, tier INT NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', gift_amount INT DEFAULT NULL, duration INT DEFAULT NULL, INDEX IDX_3BAE0AA74848F12A (broadcaster_id), INDEX IDX_3BAE0AA78F0D6EA3 (webhook_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', streamer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, first_name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, zip INT NOT NULL, country VARCHAR(255) NOT NULL, note LONGTEXT DEFAULT NULL, delivery_price INT NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), INDEX IDX_F529939825F432AD (streamer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', order_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', quantity INT NOT NULL, title VARCHAR(255) NOT NULL, price INT NOT NULL, price_vat INT NOT NULL, vat INT NOT NULL, INDEX IDX_52EA1F094584665A (product_id), INDEX IDX_52EA1F098D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', streamer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price INT NOT NULL, cumulative_months INT DEFAULT NULL, current_streak INT DEFAULT NULL, max_streak INT DEFAULT NULL, gifted_total INT DEFAULT NULL, tier INT DEFAULT NULL, price_vat INT NOT NULL, vat INT NOT NULL, subscriber TINYINT(1) NOT NULL, order_limit INT DEFAULT NULL, INDEX IDX_D34A04AD25F432AD (streamer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_image (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', path LONGTEXT NOT NULL, thumbnail LONGTEXT NOT NULL, name VARCHAR(255) NOT NULL, size INT NOT NULL, INDEX IDX_64617F034584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scope (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', scope VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriber (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', streamer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', twitch_id INT NOT NULL, cumulative_months INT DEFAULT NULL, current_streak INT DEFAULT NULL, max_streak INT DEFAULT NULL, gifted_total INT DEFAULT NULL, tier INT NOT NULL, INDEX IDX_AD005B6925F432AD (streamer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id INT NOT NULL, user_name VARCHAR(255) NOT NULL, access_token VARCHAR(255) NOT NULL, refresh_token VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, streamer TINYINT(1) NOT NULL, image LONGTEXT DEFAULT NULL, twitch_authorized TINYINT(1) NOT NULL, UNIQUE INDEX email_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_scope (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', scope_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_E26DAE8BA76ED395 (user_id), INDEX IDX_E26DAE8B682B5931 (scope_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE webhook_event (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', twitch_id VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B17EEFDEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA74848F12A FOREIGN KEY (broadcaster_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA78F0D6EA3 FOREIGN KEY (webhook_event_id) REFERENCES webhook_event (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939825F432AD FOREIGN KEY (streamer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD25F432AD FOREIGN KEY (streamer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE subscriber ADD CONSTRAINT FK_AD005B6925F432AD FOREIGN KEY (streamer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_scope ADD CONSTRAINT FK_E26DAE8BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_scope ADD CONSTRAINT FK_E26DAE8B682B5931 FOREIGN KEY (scope_id) REFERENCES scope (id)');
        $this->addSql('ALTER TABLE webhook_event ADD CONSTRAINT FK_B17EEFDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
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
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA74848F12A');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA78F0D6EA3');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939825F432AD');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F094584665A');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD25F432AD');
        $this->addSql('ALTER TABLE product_image DROP FOREIGN KEY FK_64617F034584665A');
        $this->addSql('ALTER TABLE subscriber DROP FOREIGN KEY FK_AD005B6925F432AD');
        $this->addSql('ALTER TABLE user_scope DROP FOREIGN KEY FK_E26DAE8BA76ED395');
        $this->addSql('ALTER TABLE user_scope DROP FOREIGN KEY FK_E26DAE8B682B5931');
        $this->addSql('ALTER TABLE webhook_event DROP FOREIGN KEY FK_B17EEFDEA76ED395');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('DROP TABLE scope');
        $this->addSql('DROP TABLE subscriber');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_scope');
        $this->addSql('DROP TABLE webhook_event');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
