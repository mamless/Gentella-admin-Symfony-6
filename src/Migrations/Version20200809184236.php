<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200809184236 extends AbstractMigration
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
        $this->addSql('ALTER TABLE historique DROP INDEX IDX_EDBFD5EC381CDA7C, ADD UNIQUE INDEX UNIQ_EDBFD5EC381CDA7C (old_post_id)');
        $this->addSql('ALTER TABLE historique CHANGE old_post_id old_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE old_post CHANGE published_at published_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_post CHANGE plubished_at plubished_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE categorie CHANGE categorie_parente_id categorie_parente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE historique DROP INDEX UNIQ_EDBFD5EC381CDA7C, ADD INDEX IDX_EDBFD5EC381CDA7C (old_post_id)');
        $this->addSql('ALTER TABLE historique CHANGE old_post_id old_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE old_post CHANGE published_at published_at DATETIME DEFAULT \'NULL\'');
    }
}
