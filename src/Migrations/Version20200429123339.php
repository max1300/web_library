<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200429123339 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic_framework (id INT NOT NULL, framework_id INT NOT NULL, UNIQUE INDEX UNIQ_74E3FAA137AECF72 (framework_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic_programming_language (id INT NOT NULL, programming_language_id INT NOT NULL, UNIQUE INDEX UNIQ_F6B03797A2574C1E (programming_language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE topic_framework ADD CONSTRAINT FK_74E3FAA137AECF72 FOREIGN KEY (framework_id) REFERENCES framework (id)');
        $this->addSql('ALTER TABLE topic_framework ADD CONSTRAINT FK_74E3FAA1BF396750 FOREIGN KEY (id) REFERENCES topic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE topic_programming_language ADD CONSTRAINT FK_F6B03797A2574C1E FOREIGN KEY (programming_language_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE topic_programming_language ADD CONSTRAINT FK_F6B03797BF396750 FOREIGN KEY (id) REFERENCES topic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ressource ADD topic_id INT NOT NULL');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F45441F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('CREATE INDEX IDX_939F45441F55203D ON ressource (topic_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F45441F55203D');
        $this->addSql('ALTER TABLE topic_framework DROP FOREIGN KEY FK_74E3FAA1BF396750');
        $this->addSql('ALTER TABLE topic_programming_language DROP FOREIGN KEY FK_F6B03797BF396750');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE topic_framework');
        $this->addSql('DROP TABLE topic_programming_language');
        $this->addSql('DROP INDEX IDX_939F45441F55203D ON ressource');
        $this->addSql('ALTER TABLE ressource DROP topic_id');
    }
}
