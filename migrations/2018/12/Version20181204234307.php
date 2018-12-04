<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181204234307 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (email_address VARCHAR(255) NOT NULL COMMENT \'(DC2Type:Caloriary\\\\Authentication\\\\Value\\\\EmailAddress)\', password_hash VARCHAR(255) NOT NULL COMMENT \'(DC2Type:Caloriary\\\\Authentication\\\\Value\\\\PasswordHash)\', role VARCHAR(255) NOT NULL COMMENT \'(DC2Type:Caloriary\\\\Authorization\\\\Value\\\\UserRole)\', daily_limit INT NOT NULL COMMENT \'(DC2Type:Caloriary\\\\Calories\\\\Value\\\\DailyCaloriesLimit)\', PRIMARY KEY(email_address)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user');
    }
}
