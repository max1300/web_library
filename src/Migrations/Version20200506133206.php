<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200506133206 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, ressource_id INT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9474526CFC6CD52A (ressource_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE framework (id INT AUTO_INCREMENT NOT NULL, program_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, doc_url VARCHAR(255) NOT NULL, INDEX IDX_9D766E193EB8070A (program_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE level (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, level_id INT NOT NULL, topic_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, INDEX IDX_939F4544F675F31B (author_id), INDEX IDX_939F45445FB14BA7 (level_id), INDEX IDX_939F45441F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic_framework (id INT NOT NULL, framework_id INT NOT NULL, UNIQUE INDEX UNIQ_74E3FAA137AECF72 (framework_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic_programming_language (id INT NOT NULL, programming_language_id INT NOT NULL, UNIQUE INDEX UNIQ_F6B03797A2574C1E (programming_language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, login VARCHAR(255) NOT NULL, profil_pic VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE framework ADD CONSTRAINT FK_9D766E193EB8070A FOREIGN KEY (program_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F4544F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F45445FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F45441F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE topic_framework ADD CONSTRAINT FK_74E3FAA137AECF72 FOREIGN KEY (framework_id) REFERENCES framework (id)');
        $this->addSql('ALTER TABLE topic_framework ADD CONSTRAINT FK_74E3FAA1BF396750 FOREIGN KEY (id) REFERENCES topic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE topic_programming_language ADD CONSTRAINT FK_F6B03797A2574C1E FOREIGN KEY (programming_language_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE topic_programming_language ADD CONSTRAINT FK_F6B03797BF396750 FOREIGN KEY (id) REFERENCES topic (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F4544F675F31B');
        $this->addSql('ALTER TABLE topic_framework DROP FOREIGN KEY FK_74E3FAA137AECF72');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F45445FB14BA7');
        $this->addSql('ALTER TABLE framework DROP FOREIGN KEY FK_9D766E193EB8070A');
        $this->addSql('ALTER TABLE topic_programming_language DROP FOREIGN KEY FK_F6B03797A2574C1E');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CFC6CD52A');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F45441F55203D');
        $this->addSql('ALTER TABLE topic_framework DROP FOREIGN KEY FK_74E3FAA1BF396750');
        $this->addSql('ALTER TABLE topic_programming_language DROP FOREIGN KEY FK_F6B03797BF396750');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE framework');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE topic_framework');
        $this->addSql('DROP TABLE topic_programming_language');
        $this->addSql('DROP TABLE user');
    }
}
