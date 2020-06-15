<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200416163034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE community ADD admin_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE community ADD CONSTRAINT FK_1B604033DF6E65AD FOREIGN KEY (admin_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1B604033DF6E65AD ON community (admin_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE community DROP FOREIGN KEY FK_1B604033DF6E65AD');
        $this->addSql('DROP INDEX IDX_1B604033DF6E65AD ON community');
        $this->addSql('ALTER TABLE community DROP admin_id_id');
    }
}
