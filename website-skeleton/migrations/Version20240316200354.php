<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240316200354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, post_id INT DEFAULT NULL, INDEX IDX_AC6340B3A76ED395 (user_id), INDEX IDX_AC6340B34B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B34B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post ADD likes_id INT DEFAULT NULL, CHANGE attachment attachment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D2F23775F FOREIGN KEY (likes_id) REFERENCES `like` (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D2F23775F ON post (likes_id)');
        $this->addSql('ALTER TABLE user ADD likes_id INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE profile_picture_filename profile_picture_filename VARCHAR(255) DEFAULT NULL, CHANGE cover_photo_filename cover_photo_filename VARCHAR(255) DEFAULT NULL, CHANGE job job VARCHAR(255) DEFAULT NULL, CHANGE school school VARCHAR(255) DEFAULT NULL, CHANGE interests interests VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492F23775F FOREIGN KEY (likes_id) REFERENCES `like` (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492F23775F ON user (likes_id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D2F23775F');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492F23775F');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B34B89032C');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('DROP INDEX IDX_5A8A6C8D2F23775F ON post');
        $this->addSql('ALTER TABLE post DROP likes_id, CHANGE attachment attachment VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('DROP INDEX IDX_8D93D6492F23775F ON `user`');
        $this->addSql('ALTER TABLE `user` DROP likes_id, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE profile_picture_filename profile_picture_filename VARCHAR(255) DEFAULT \'NULL\', CHANGE cover_photo_filename cover_photo_filename VARCHAR(255) DEFAULT \'NULL\', CHANGE job job VARCHAR(255) DEFAULT \'NULL\', CHANGE school school VARCHAR(255) DEFAULT \'NULL\', CHANGE interests interests VARCHAR(255) DEFAULT \'NULL\'');
    }
}
