<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220809170256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE is_deleted is_deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD98260155 ON product (region_id)');
        $this->addSql('DROP INDEX name ON region');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD98260155');
        $this->addSql('DROP INDEX UNIQ_D34A04AD98260155 ON product');
        $this->addSql('ALTER TABLE product CHANGE is_deleted is_deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX name ON region (name)');
    }
}
