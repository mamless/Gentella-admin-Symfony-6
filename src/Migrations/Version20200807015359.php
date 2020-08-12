<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200807015359 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_post CHANGE plubished_at plubished_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie CHANGE categorie_parente_id categorie_parente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE historique ADD user_id INT NOT NULL, ADD blog_post_id INT NOT NULL, ADD last_content LONGTEXT NOT NULL, ADD action VARCHAR(255) NOT NULL, ADD action_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5ECA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('CREATE INDEX IDX_EDBFD5ECA76ED395 ON historique (user_id)');
        $this->addSql('CREATE INDEX IDX_EDBFD5ECA77FBEAF ON historique (blog_post_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_post CHANGE plubished_at plubished_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE categorie CHANGE categorie_parente_id categorie_parente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5ECA76ED395');
        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5ECA77FBEAF');
        $this->addSql('DROP INDEX IDX_EDBFD5ECA76ED395 ON historique');
        $this->addSql('DROP INDEX IDX_EDBFD5ECA77FBEAF ON historique');
        $this->addSql('ALTER TABLE historique DROP user_id, DROP blog_post_id, DROP last_content, DROP action, DROP action_date');
    }
}
