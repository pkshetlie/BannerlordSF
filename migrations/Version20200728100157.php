<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200728100157 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD api_key VARCHAR(255) NOT NULL, ADD is_verified TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user_score ADD user_id INT NOT NULL, ADD run_number INT NOT NULL, ADD challenge_edition INT NOT NULL, DROP api_key');
        $this->addSql('ALTER TABLE user_score ADD CONSTRAINT FK_D05BCC09A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D05BCC09A76ED395 ON user_score (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP api_key, DROP is_verified');
        $this->addSql('ALTER TABLE user_score DROP FOREIGN KEY FK_D05BCC09A76ED395');
        $this->addSql('DROP INDEX IDX_D05BCC09A76ED395 ON user_score');
        $this->addSql('ALTER TABLE user_score ADD api_key VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP user_id, DROP run_number, DROP challenge_edition');
    }
}
