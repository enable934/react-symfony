<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191216222814 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orders ADD restaurant_id INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEB1E7706E ON orders (restaurant_id)');
        $this->addSql('ALTER TABLE tables ADD restaurant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tables ADD CONSTRAINT FK_84470221B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE INDEX IDX_84470221B1E7706E ON tables (restaurant_id)');
        $this->addSql('ALTER TABLE user ADD restaurant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B1E7706E ON user (restaurant_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEB1E7706E');
        $this->addSql('ALTER TABLE tables DROP FOREIGN KEY FK_84470221B1E7706E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B1E7706E');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP INDEX IDX_E52FFDEEB1E7706E ON orders');
        $this->addSql('ALTER TABLE orders DROP restaurant_id');
        $this->addSql('DROP INDEX IDX_84470221B1E7706E ON tables');
        $this->addSql('ALTER TABLE tables DROP restaurant_id');
        $this->addSql('DROP INDEX IDX_8D93D649B1E7706E ON user');
        $this->addSql('ALTER TABLE user DROP restaurant_id');
    }
}
