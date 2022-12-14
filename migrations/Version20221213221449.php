<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221213221449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE factions_applicants (faction_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_314EB2134448F8DA (faction_id), INDEX IDX_314EB213A76ED395 (user_id), PRIMARY KEY(faction_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_czech_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE factions_applicants ADD CONSTRAINT FK_314EB2134448F8DA FOREIGN KEY (faction_id) REFERENCES `factions` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE factions_applicants ADD CONSTRAINT FK_314EB213A76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE factions_applicants DROP FOREIGN KEY FK_314EB2134448F8DA');
        $this->addSql('ALTER TABLE factions_applicants DROP FOREIGN KEY FK_314EB213A76ED395');
        $this->addSql('DROP TABLE factions_applicants');
    }
}
