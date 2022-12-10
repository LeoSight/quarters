<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221210184748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `factions` (id INT AUTO_INCREMENT NOT NULL, leader_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, color VARCHAR(6) NOT NULL, flag VARCHAR(255) DEFAULT NULL, INDEX IDX_EB8258C473154ED4 (leader_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_czech_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `factions` ADD CONSTRAINT FK_EB8258C473154ED4 FOREIGN KEY (leader_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE users ADD faction_id INT DEFAULT NULL, ADD x INT NOT NULL, ADD y INT NOT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E94448F8DA FOREIGN KEY (faction_id) REFERENCES `factions` (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E94448F8DA ON users (faction_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `users` DROP FOREIGN KEY FK_1483A5E94448F8DA');
        $this->addSql('ALTER TABLE `factions` DROP FOREIGN KEY FK_EB8258C473154ED4');
        $this->addSql('DROP TABLE `factions`');
        $this->addSql('DROP INDEX IDX_1483A5E94448F8DA ON `users`');
        $this->addSql('ALTER TABLE `users` DROP faction_id, DROP x, DROP y');
    }
}
