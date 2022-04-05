<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404140050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE archives_sorties (id INT AUTO_INCREMENT NOT NULL, nom_sortie VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, duree INT DEFAULT NULL, date_fin_inscription DATETIME NOT NULL, nb_inscriptions_max INT NOT NULL, description LONGTEXT DEFAULT NULL, pseudo_organisateur VARCHAR(255) NOT NULL, nom_organisateur VARCHAR(255) NOT NULL, prenom_organisateur VARCHAR(255) NOT NULL, nom_lieu VARCHAR(255) NOT NULL, cp_ville VARCHAR(5) NOT NULL, nom_ville VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {

    }
}
