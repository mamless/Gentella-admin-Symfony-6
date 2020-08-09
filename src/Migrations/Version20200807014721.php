<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200807014721 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE historique (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_post CHANGE plubished_at plubished_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie CHANGE categorie_parente_id categorie_parente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE admin admin TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE historique');
        $this->addSql('ALTER TABLE blog_post CHANGE plubished_at plubished_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE categorie CHANGE categorie_parente_id categorie_parente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE admin admin TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
