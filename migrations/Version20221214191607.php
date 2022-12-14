<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221214191607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `fields` (id INT AUTO_INCREMENT NOT NULL, x INT NOT NULL, y INT NOT NULL, type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_czech_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `towns` (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, x INT NOT NULL, y INT NOT NULL, INDEX IDX_CAF94E3D7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_czech_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `towns` ADD CONSTRAINT FK_CAF94E3D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `factions` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `towns` DROP FOREIGN KEY FK_CAF94E3D7E3C61F9');
        $this->addSql('DROP TABLE `fields`');
        $this->addSql('DROP TABLE `towns`');
    }
}
