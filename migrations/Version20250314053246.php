<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250314053246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B9D86650F');
        $this->addSql('DROP INDEX UNIQ_2246507B9D86650F ON basket');
        $this->addSql('ALTER TABLE basket CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507BA76ED395 ON basket (user_id)');
        $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY FK_17ED14B4293CD56D');
        $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY FK_17ED14B4DE18E50B');
        $this->addSql('DROP INDEX IDX_17ED14B4293CD56D ON basket_product');
        $this->addSql('DROP INDEX IDX_17ED14B4DE18E50B ON basket_product');
        $this->addSql('ALTER TABLE basket_product ADD basket_id INT NOT NULL, ADD product_id INT NOT NULL, DROP basket_id_id, DROP product_id_id');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B41BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id)');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B44584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_17ED14B41BE1FB52 ON basket_product (basket_id)');
        $this->addSql('CREATE INDEX IDX_17ED14B44584665A ON basket_product (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BA76ED395');
        $this->addSql('DROP INDEX UNIQ_2246507BA76ED395 ON basket');
        $this->addSql('ALTER TABLE basket CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507B9D86650F ON basket (user_id_id)');
        $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY FK_17ED14B41BE1FB52');
        $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY FK_17ED14B44584665A');
        $this->addSql('DROP INDEX IDX_17ED14B41BE1FB52 ON basket_product');
        $this->addSql('DROP INDEX IDX_17ED14B44584665A ON basket_product');
        $this->addSql('ALTER TABLE basket_product ADD basket_id_id INT NOT NULL, ADD product_id_id INT NOT NULL, DROP basket_id, DROP product_id');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B4293CD56D FOREIGN KEY (basket_id_id) REFERENCES basket (id)');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B4DE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_17ED14B4293CD56D ON basket_product (basket_id_id)');
        $this->addSql('CREATE INDEX IDX_17ED14B4DE18E50B ON basket_product (product_id_id)');
    }
}
