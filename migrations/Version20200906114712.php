<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200906114712 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statistic (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', kcal INT DEFAULT NULL, max_width INT DEFAULT NULL, min_width INT DEFAULT NULL, max_speed DOUBLE PRECISION DEFAULT NULL, min_speed DOUBLE PRECISION DEFAULT NULL, duration DATETIME DEFAULT NULL, number_of_breaks INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, weight INT DEFAULT NULL, sex TINYINT(1) DEFAULT NULL, growth INT DEFAULT NULL, is_public TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', profile_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, edited_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', statistic_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D5128A8F53B6268F (statistic_id), INDEX IDX_D5128A8FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gps_log (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', training_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', langitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, speed DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, is_stop TINYINT(1) NOT NULL, is_paused TINYINT(1) NOT NULL, INDEX IDX_853D94E8BEFD98D1 (training_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE generated_url (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', hash VARCHAR(255) NOT NULL, type INT NOT NULL, expired_at DATETIME NOT NULL, entry INT NOT NULL, INDEX IDX_8F8880F4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8F53B6268F FOREIGN KEY (statistic_id) REFERENCES statistic (id)');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gps_log ADD CONSTRAINT FK_853D94E8BEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id)');
        $this->addSql('ALTER TABLE generated_url ADD CONSTRAINT FK_8F8880F4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649CCFA12B8');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8F53B6268F');
        $this->addSql('ALTER TABLE gps_log DROP FOREIGN KEY FK_853D94E8BEFD98D1');
        $this->addSql('ALTER TABLE generated_url DROP FOREIGN KEY FK_8F8880F4A76ED395');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8FA76ED395');
        $this->addSql('DROP TABLE generated_url');
        $this->addSql('DROP TABLE gps_log');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE statistic');
        $this->addSql('DROP TABLE training');
        $this->addSql('DROP TABLE user');
    }
}
