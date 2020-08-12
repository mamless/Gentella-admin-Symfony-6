<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200806220840 extends AbstractMigration
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
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blog_post_categorie ADD CONSTRAINT FK_AC64037AA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post_categorie ADD CONSTRAINT FK_AC64037ABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie CHANGE categorie_parente_id categorie_parente_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_post_categorie DROP FOREIGN KEY FK_AC64037AA77FBEAF');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE blog_post_categorie');
        $this->addSql('ALTER TABLE categorie CHANGE categorie_parente_id categorie_parente_id INT DEFAULT NULL');
    }
}
