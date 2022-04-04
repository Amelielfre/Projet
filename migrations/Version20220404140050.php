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
        $this->addSql('ALTER TABLE archives_sorties ADD date_debut DATETIME NOT NULL, ADD date_fin_inscription DATETIME NOT NULL, ADD nom_lieu VARCHAR(255) NOT NULL, ADD cp_ville VARCHAR(5) NOT NULL, ADD nom_ville VARCHAR(255) NOT NULL, DROP date_heure_debut, DROP date_limite_inscription, CHANGE duree duree INT DEFAULT NULL, CHANGE nb_inscription_max nb_inscriptions_max INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE archives_sorties ADD date_heure_debut DATETIME NOT NULL, ADD date_limite_inscription DATETIME NOT NULL, DROP date_debut, DROP date_fin_inscription, DROP nom_lieu, DROP cp_ville, DROP nom_ville, CHANGE duree duree INT NOT NULL, CHANGE nb_inscriptions_max nb_inscription_max INT NOT NULL');
    }
}
