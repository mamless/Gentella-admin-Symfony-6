<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200811134927 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, creator_id INT NOT NULL, content LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, blog_image VARCHAR(255) NOT NULL, plubished_at DATETIME DEFAULT NULL, deleted TINYINT(1) NOT NULL, valid TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_BA5AE01D989D9B62 (slug), UNIQUE INDEX UNIQ_BA5AE01DFF7747B4 (titre), INDEX IDX_BA5AE01DF675F31B (author_id), INDEX IDX_BA5AE01D61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post_categorie (blog_post_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_AC64037AA77FBEAF (blog_post_id), INDEX IDX_AC64037ABCF5E72D (categorie_id), PRIMARY KEY(blog_post_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historique (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, blog_post_id INT NOT NULL, old_post_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, action_date DATETIME NOT NULL, INDEX IDX_EDBFD5ECA76ED395 (user_id), INDEX IDX_EDBFD5ECA77FBEAF (blog_post_id), UNIQUE INDEX UNIQ_EDBFD5EC381CDA7C (old_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE old_post (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, author_id INT NOT NULL, created_at DATETIME NOT NULL, published_at DATETIME DEFAULT NULL, titre VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_7217ED28B03A8386 (created_by_id), INDEX IDX_7217ED28F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE old_post_categorie (old_post_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_9E9BAB81381CDA7C (old_post_id), INDEX IDX_9E9BAB81BCF5E72D (categorie_id), PRIMARY KEY(old_post_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, categorie_parente_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, valid TINYINT(1) NOT NULL, deleted TINYINT(1) NOT NULL, INDEX IDX_497DD6345CBD743C (categorie_parente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', nom_complet VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, valid TINYINT(1) NOT NULL, deleted TINYINT(1) NOT NULL, password VARCHAR(255) NOT NULL, admin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, role_name VARCHAR(100) NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blog_post_categorie ADD CONSTRAINT FK_AC64037AA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post_categorie ADD CONSTRAINT FK_AC64037ABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5ECA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5EC381CDA7C FOREIGN KEY (old_post_id) REFERENCES old_post (id)');
        $this->addSql('ALTER TABLE old_post ADD CONSTRAINT FK_7217ED28B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE old_post ADD CONSTRAINT FK_7217ED28F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE old_post_categorie ADD CONSTRAINT FK_9E9BAB81381CDA7C FOREIGN KEY (old_post_id) REFERENCES old_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE old_post_categorie ADD CONSTRAINT FK_9E9BAB81BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD6345CBD743C FOREIGN KEY (categorie_parente_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_post_categorie DROP FOREIGN KEY FK_AC64037AA77FBEAF');
        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5ECA77FBEAF');
        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5EC381CDA7C');
        $this->addSql('ALTER TABLE old_post_categorie DROP FOREIGN KEY FK_9E9BAB81381CDA7C');
        $this->addSql('ALTER TABLE blog_post_categorie DROP FOREIGN KEY FK_AC64037ABCF5E72D');
        $this->addSql('ALTER TABLE old_post_categorie DROP FOREIGN KEY FK_9E9BAB81BCF5E72D');
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD6345CBD743C');
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01DF675F31B');
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01D61220EA6');
        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5ECA76ED395');
        $this->addSql('ALTER TABLE old_post DROP FOREIGN KEY FK_7217ED28B03A8386');
        $this->addSql('ALTER TABLE old_post DROP FOREIGN KEY FK_7217ED28F675F31B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE blog_post_categorie');
        $this->addSql('DROP TABLE historique');
        $this->addSql('DROP TABLE old_post');
        $this->addSql('DROP TABLE old_post_categorie');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE role');
    }
}
