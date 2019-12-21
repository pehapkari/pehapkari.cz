<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 * Created from empty database, @see: https://symfonycasts.com/screencast/symfony3-doctrine/migrations#the-migrations-workflow
 */
final class Version20191221130138 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE marketing_event (id INT AUTO_INCREMENT NOT NULL, training_term_id INT DEFAULT NULL, platform VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, planned_at DATETIME NOT NULL, published_at DATETIME DEFAULT NULL, is_done TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5E9CF483D312A5F2 (training_term_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D6495E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, training_term_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, note VARCHAR(255) NOT NULL, partner VARCHAR(255) NOT NULL, INDEX IDX_2D3A8DA6D312A5F2 (training_term_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_term (id INT AUTO_INCREMENT NOT NULL, training_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, is_provision_paid TINYINT(1) NOT NULL, is_provision_email_sent TINYINT(1) NOT NULL, are_feedback_emails_sent TINYINT(1) NOT NULL, start_date_time DATETIME NOT NULL, UNIQUE INDEX UNIQ_AA95A0CE989D9B62 (slug), INDEX IDX_AA95A0CEBEFD98D1 (training_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trainer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, position VARCHAR(255) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, bio LONGTEXT NOT NULL, twitter_name VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, image_uploaded_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training (id INT AUTO_INCREMENT NOT NULL, trainer_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, certificate_formatted_name LONGTEXT DEFAULT NULL, perex LONGTEXT NOT NULL, description LONGTEXT NOT NULL, hashtags VARCHAR(255) DEFAULT NULL, duration INT NOT NULL, price INT NOT NULL, image VARCHAR(255) DEFAULT NULL, image_uploaded_at DATETIME DEFAULT NULL, is_public TINYINT(1) NOT NULL, slug VARCHAR(255) DEFAULT NULL, INDEX IDX_D5128A8FFB08EDF6 (trainer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_feedback (id INT AUTO_INCREMENT NOT NULL, training_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, point_of_entry VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, text LONGTEXT NOT NULL, rating DOUBLE PRECISION DEFAULT NULL, things_to_improve LONGTEXT DEFAULT NULL, is_agreed_with_publishing_name TINYINT(1) NOT NULL, is_shown_on_main_page TINYINT(1) NOT NULL, is_public TINYINT(1) NOT NULL, is_revised TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_354C57CFBEFD98D1 (training_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_registration (id INT AUTO_INCREMENT NOT NULL, training_term_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, ico VARCHAR(255) DEFAULT NULL, note LONGTEXT DEFAULT NULL, participant_count INT NOT NULL, has_invoice TINYINT(1) NOT NULL, agrees_with_personal_data TINYINT(1) NOT NULL, price DOUBLE PRECISION NOT NULL, fakturoid_invoice_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_7ABB8BAAD312A5F2 (training_term_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE marketing_event ADD CONSTRAINT FK_5E9CF483D312A5F2 FOREIGN KEY (training_term_id) REFERENCES training_term (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6D312A5F2 FOREIGN KEY (training_term_id) REFERENCES training_term (id)');
        $this->addSql('ALTER TABLE training_term ADD CONSTRAINT FK_AA95A0CEBEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id)');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8FFB08EDF6 FOREIGN KEY (trainer_id) REFERENCES trainer (id)');
        $this->addSql('ALTER TABLE training_feedback ADD CONSTRAINT FK_354C57CFBEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id)');
        $this->addSql('ALTER TABLE training_registration ADD CONSTRAINT FK_7ABB8BAAD312A5F2 FOREIGN KEY (training_term_id) REFERENCES training_term (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE marketing_event DROP FOREIGN KEY FK_5E9CF483D312A5F2');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6D312A5F2');
        $this->addSql('ALTER TABLE training_registration DROP FOREIGN KEY FK_7ABB8BAAD312A5F2');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8FFB08EDF6');
        $this->addSql('ALTER TABLE training_term DROP FOREIGN KEY FK_AA95A0CEBEFD98D1');
        $this->addSql('ALTER TABLE training_feedback DROP FOREIGN KEY FK_354C57CFBEFD98D1');
        $this->addSql('DROP TABLE marketing_event');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE training_term');
        $this->addSql('DROP TABLE trainer');
        $this->addSql('DROP TABLE training');
        $this->addSql('DROP TABLE training_feedback');
        $this->addSql('DROP TABLE training_registration');
    }
}
