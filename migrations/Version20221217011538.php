<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221217011538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `notifications` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created DATETIME NOT NULL, type INT NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_6000B0D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_czech_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `notifications` ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `notifications` DROP FOREIGN KEY FK_6000B0D3A76ED395');
        $this->addSql('DROP TABLE `notifications`');
    }
}
