<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230903181138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__customer_order AS SELECT id, reference, customer_name, customer_address, status, created_at FROM customer_order');
        $this->addSql('DROP TABLE customer_order');
        $this->addSql('CREATE TABLE customer_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, delivery_option_id INTEGER NOT NULL, reference VARCHAR(255) NOT NULL, customer_name VARCHAR(255) NOT NULL, customer_address CLOB NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, estimated_delivery_date_time DATETIME NOT NULL, CONSTRAINT FK_3B1CE6A3E3A151FD FOREIGN KEY (delivery_option_id) REFERENCES delivery_option (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO customer_order (id, reference, customer_name, customer_address, status, created_at) SELECT id, reference, customer_name, customer_address, status, created_at FROM __temp__customer_order');
        $this->addSql('DROP TABLE __temp__customer_order');
        $this->addSql('CREATE INDEX IDX_3B1CE6A3E3A151FD ON customer_order (delivery_option_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__customer_order AS SELECT id, reference, customer_name, customer_address, status, created_at FROM customer_order');
        $this->addSql('DROP TABLE customer_order');
        $this->addSql('CREATE TABLE customer_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reference VARCHAR(255) NOT NULL, customer_name VARCHAR(255) NOT NULL, customer_address CLOB NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO customer_order (id, reference, customer_name, customer_address, status, created_at) SELECT id, reference, customer_name, customer_address, status, created_at FROM __temp__customer_order');
        $this->addSql('DROP TABLE __temp__customer_order');
    }
}
