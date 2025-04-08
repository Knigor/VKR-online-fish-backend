<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408145653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE order_items (id SERIAL NOT NULL, order_id_id INT DEFAULT NULL, product_id_id INT DEFAULT NULL, quantity INT NOT NULL, price_order INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_62809DB0FCDAEAAA ON order_items (order_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_62809DB0DE18E50B ON order_items (product_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0FCDAEAAA FOREIGN KEY (order_id_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0DE18E50B FOREIGN KEY (product_id_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items DROP CONSTRAINT FK_62809DB0FCDAEAAA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items DROP CONSTRAINT FK_62809DB0DE18E50B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE order_items
        SQL);
    }
}
