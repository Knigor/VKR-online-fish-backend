<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408144604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE orders ADD users_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E52FFDEE67B3B43D ON orders (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD users_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3BA5A5A67B3B43D ON products (users_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E52FFDEE67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders DROP users_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5A67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B3BA5A5A67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products DROP users_id
        SQL);
    }
}
