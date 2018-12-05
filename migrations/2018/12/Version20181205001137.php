<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181205001137 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE caloric_record (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:Caloriary\\\\Calories\\\\Value\\\\CaloricRecordId)\', owner_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:Caloriary\\\\Authentication\\\\Value\\\\EmailAddress)\', calories INT NOT NULL COMMENT \'(DC2Type:Caloriary\\\\Calories\\\\Value\\\\Calories)\', ate_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', text LONGTEXT NOT NULL COMMENT \'(DC2Type:Caloriary\\\\Calories\\\\Value\\\\MealDescription)\', INDEX IDX_BF65F1F77E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE caloric_record ADD CONSTRAINT FK_BF65F1F77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (email_address)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE caloric_record');
    }
}
