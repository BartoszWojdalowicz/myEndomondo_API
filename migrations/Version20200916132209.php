<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200916132209 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, gps_log_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', training_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, is_public TINYINT(1) NOT NULL, is_main TINYINT(1) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_C53D045F8CF547A2 (gps_log_id), INDEX IDX_C53D045FBEFD98D1 (training_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F8CF547A2 FOREIGN KEY (gps_log_id) REFERENCES gps_log (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FBEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE image');
    }
}
