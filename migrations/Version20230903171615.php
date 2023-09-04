<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230903171615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Intial entity creation and sample data inserts';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reference VARCHAR(255) NOT NULL, customer_name VARCHAR(255) NOT NULL, customer_address CLOB NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE customer_order_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_order_id INTEGER NOT NULL, item_id INTEGER NOT NULL, quantity INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, CONSTRAINT FK_AF231B8BA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_AF231B8B126F525E FOREIGN KEY (item_id) REFERENCES item (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_AF231B8BA15A2E17 ON customer_order_item (customer_order_id)');
        $this->addSql('CREATE INDEX IDX_AF231B8B126F525E ON customer_order_item (item_id)');
        $this->addSql('CREATE TABLE delivery_option (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lead_days INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, price DOUBLE PRECISION NOT NULL)');
        $this->addSql('CREATE TABLE order_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL)');

        // Create example data
        $this->addSql('INSERT INTO delivery_option (name, lead_days) VALUES ("Royal Mail", 7)');
        $this->addSql('INSERT INTO delivery_option (name, lead_days) VALUES ("DHL", 2)');

        $this->addSql('INSERT INTO item (name, price) VALUES ("Bike", 199.99)');
        $this->addSql('INSERT INTO item (name, price) VALUES ("PC", 799.99)');
        $this->addSql('INSERT INTO item (name, price) VALUES ("Teddy", 9.49)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE customer_order');
        $this->addSql('DROP TABLE customer_order_item');
        $this->addSql('DROP TABLE delivery_option');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE order_status');
    }
}
