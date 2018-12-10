<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181210002106 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE caloric_record DROP FOREIGN KEY FK_BF65F1F77E3C61F9');
        $this->addSql('ALTER TABLE caloric_record ADD CONSTRAINT FK_BF65F1F77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (email_address) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE caloric_record DROP FOREIGN KEY FK_BF65F1F77E3C61F9');
        $this->addSql('ALTER TABLE caloric_record ADD CONSTRAINT FK_BF65F1F77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (email_address)');
    }
}
