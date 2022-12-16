<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221216175250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `items` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, item_id INT NOT NULL, quantity INT NOT NULL, x INT DEFAULT NULL, y INT DEFAULT NULL, INDEX IDX_E11EE94DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_czech_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `items` ADD CONSTRAINT FK_E11EE94DA76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE users ADD kills INT DEFAULT 0 NOT NULL, ADD deaths INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `items` DROP FOREIGN KEY FK_E11EE94DA76ED395');
        $this->addSql('DROP TABLE `items`');
        $this->addSql('ALTER TABLE `users` DROP kills, DROP deaths');
    }
}
