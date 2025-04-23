<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422080837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items DROP CONSTRAINT fk_62809db0de18e50b
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_62809db0de18e50b
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items RENAME COLUMN product_id_id TO product_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_62809DB04584665A ON order_items (product_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items DROP CONSTRAINT FK_62809DB04584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_62809DB04584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items RENAME COLUMN product_id TO product_id_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items ADD CONSTRAINT fk_62809db0de18e50b FOREIGN KEY (product_id_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_62809db0de18e50b ON order_items (product_id_id)
        SQL);
    }
}
