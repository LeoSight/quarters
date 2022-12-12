<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221211182105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `actions` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type SMALLINT NOT NULL, status SMALLINT NOT NULL, run_time DATETIME NOT NULL, INDEX IDX_548F1EFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_czech_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `actions` ADD CONSTRAINT FK_548F1EFA76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE users ADD busy_till DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `actions` DROP FOREIGN KEY FK_548F1EFA76ED395');
        $this->addSql('DROP TABLE `actions`');
        $this->addSql('ALTER TABLE `users` DROP busy_till');
    }
}
