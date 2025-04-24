<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424172507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE albaran (id INT AUTO_INCREMENT NOT NULL, cliente_id INT NOT NULL, factura_id INT DEFAULT NULL, estado VARCHAR(255) NOT NULL, fecha_creacion DATETIME NOT NULL, fecha_actualizacion DATETIME DEFAULT NULL, INDEX IDX_2E6A49C2DE734E51 (cliente_id), INDEX IDX_2E6A49C2F04F795F (factura_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cliente (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, direccion VARCHAR(255) NOT NULL, fecha_creacion DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE factura (id INT AUTO_INCREMENT NOT NULL, cliente_id INT NOT NULL, importe_total DOUBLE PRECISION NOT NULL, fecha_creacion DATETIME NOT NULL, INDEX IDX_F9EBA009DE734E51 (cliente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE linea_albaran (id INT AUTO_INCREMENT NOT NULL, albaran_id INT NOT NULL, producto INT NOT NULL, nombre_producto VARCHAR(255) NOT NULL, cantidad DOUBLE PRECISION NOT NULL, precio_unitario DOUBLE PRECISION NOT NULL, fecha_creacion DATETIME NOT NULL, INDEX IDX_6FB2E38578B6FB3A (albaran_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE albaran ADD CONSTRAINT FK_2E6A49C2DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE albaran ADD CONSTRAINT FK_2E6A49C2F04F795F FOREIGN KEY (factura_id) REFERENCES factura (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE factura ADD CONSTRAINT FK_F9EBA009DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE linea_albaran ADD CONSTRAINT FK_6FB2E38578B6FB3A FOREIGN KEY (albaran_id) REFERENCES albaran (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE albaran DROP FOREIGN KEY FK_2E6A49C2DE734E51
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE albaran DROP FOREIGN KEY FK_2E6A49C2F04F795F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE factura DROP FOREIGN KEY FK_F9EBA009DE734E51
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE linea_albaran DROP FOREIGN KEY FK_6FB2E38578B6FB3A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE albaran
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cliente
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE factura
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE linea_albaran
        SQL);
    }
}
