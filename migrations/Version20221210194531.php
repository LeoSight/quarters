<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221210194531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `factions` (id INT AUTO_INCREMENT NOT NULL, leader_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, color VARCHAR(6) NOT NULL, flag VARCHAR(255) DEFAULT NULL, INDEX IDX_EB8258C473154ED4 (leader_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_czech_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `users` (id INT NOT NULL, faction_id INT DEFAULT NULL, username VARCHAR(190) NOT NULL, last_seen DATETIME NOT NULL, x INT NOT NULL, y INT NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), INDEX IDX_1483A5E94448F8DA (faction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_czech_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `factions` ADD CONSTRAINT FK_EB8258C473154ED4 FOREIGN KEY (leader_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE `users` ADD CONSTRAINT FK_1483A5E94448F8DA FOREIGN KEY (faction_id) REFERENCES `factions` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `factions` DROP FOREIGN KEY FK_EB8258C473154ED4');
        $this->addSql('ALTER TABLE `users` DROP FOREIGN KEY FK_1483A5E94448F8DA');
        $this->addSql('DROP TABLE `factions`');
        $this->addSql('DROP TABLE `users`');
    }
}
