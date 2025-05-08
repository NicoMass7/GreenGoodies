<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250331044946 extends AbstractMigration
{
  public function getDescription(): string
  {
    return '';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE basket_product ADD user_id INT DEFAULT NULL');
    $this->addSql('UPDATE basket_product bp 
                   JOIN basket b ON bp.basket_id = b.id 
                   SET bp.user_id = b.user_id');
    $this->addSql('ALTER TABLE basket_product MODIFY user_id INT NOT NULL');
    $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY FK_17ED14B41BE1FB52');
    $this->addSql('ALTER TABLE basket_product DROP COLUMN basket_id');
    $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BA76ED395');
    // $this->addSql('DROP INDEX IDX_17ED14B41BE1FB52 ON basket_product'); //error là SHOW INDEX FROM TABLE pour check si l'index exist
    $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    $this->addSql('CREATE INDEX IF NOT EXISTS IDX_17ED14B4A76ED395 ON basket_product (user_id)');
    $this->addSql('DROP TABLE basket');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('CREATE TABLE IF NOT EXISTS basket (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, INDEX IDX_2246507BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    $this->addSql('ALTER TABLE basket_product ADD basket_id INT DEFAULT NULL');
    $this->addSql('INSERT INTO basket (user_id)
                   SELECT DISTINCT user_id FROM basket_product');
    $this->addSql('UPDATE basket_product bp 
                   JOIN basket b ON bp.user_id = b.user_id 
                   SET bp.basket_id = b.id');
    $this->addSql('ALTER TABLE basket_product MODIFY basket_id INT NOT NULL');
    $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY FK_17ED14B4A76ED395');
    $this->addSql('DROP INDEX IDX_17ED14B4A76ED395 ON basket_product');
    $this->addSql('ALTER TABLE basket_product DROP COLUMN user_id');
    $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B41BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id)');
    $this->addSql('CREATE INDEX IF NOT EXISTS IDX_17ED14B41BE1FB52 ON basket_product (basket_id)');
  }
}
