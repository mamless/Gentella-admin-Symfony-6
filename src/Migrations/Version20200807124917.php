<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200807124917 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE old_post (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, author_id INT NOT NULL, created_at DATETIME NOT NULL, published_at DATETIME DEFAULT NULL, titre VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_7217ED28B03A8386 (created_by_id), INDEX IDX_7217ED28F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE old_post_categorie (old_post_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_9E9BAB81381CDA7C (old_post_id), INDEX IDX_9E9BAB81BCF5E72D (categorie_id), PRIMARY KEY(old_post_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE old_post ADD CONSTRAINT FK_7217ED28B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE old_post ADD CONSTRAINT FK_7217ED28F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE old_post_categorie ADD CONSTRAINT FK_9E9BAB81381CDA7C FOREIGN KEY (old_post_id) REFERENCES old_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE old_post_categorie ADD CONSTRAINT FK_9E9BAB81BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post CHANGE plubished_at plubished_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie CHANGE categorie_parente_id categorie_parente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE historique ADD old_post_id INT DEFAULT NULL, DROP last_content');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5EC381CDA7C FOREIGN KEY (old_post_id) REFERENCES old_post (id)');
        $this->addSql('CREATE INDEX IDX_EDBFD5EC381CDA7C ON historique (old_post_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5EC381CDA7C');
        $this->addSql('ALTER TABLE old_post_categorie DROP FOREIGN KEY FK_9E9BAB81381CDA7C');
        $this->addSql('DROP TABLE old_post');
        $this->addSql('DROP TABLE old_post_categorie');
        $this->addSql('ALTER TABLE blog_post CHANGE plubished_at plubished_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE categorie CHANGE categorie_parente_id categorie_parente_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_EDBFD5EC381CDA7C ON historique');
        $this->addSql('ALTER TABLE historique ADD last_content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', DROP old_post_id');
    }
}
