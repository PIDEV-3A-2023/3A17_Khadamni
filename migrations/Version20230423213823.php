<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230423213823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis CHANGE id_utilisateur id_utilisateur INT DEFAULT NULL, CHANGE id_evenement id_evenement INT DEFAULT NULL');
        $this->addSql('ALTER TABLE candidature CHANGE id_user id_user INT DEFAULT NULL, CHANGE id_emploi id_emploi INT DEFAULT NULL');
        $this->addSql('ALTER TABLE candidaturestage CHANGE id_user id_user INT DEFAULT NULL, CHANGE id_stage id_stage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY fk_user');
        $this->addSql('ALTER TABLE emploi CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE favoris CHANGE id_emploi id_emploi INT DEFAULT NULL, CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE formation CHANGE id_formateur id_formateur INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription CHANGE id_formation id_formation INT DEFAULT NULL, CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prerequis CHANGE id_stage id_stage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD nbr_vue INT DEFAULT NULL');
        $this->addSql('ALTER TABLE role CHANGE id_role id_role INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE stage CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE suivi_reclamation DROP nbr_vue, CHANGE id_reclamation id_reclamation INT DEFAULT NULL, CHANGE etat_reclamation etat_reclamation VARCHAR(45) NOT NULL, CHANGE sujet sujet VARCHAR(255) NOT NULL, CHANGE motif motif VARCHAR(255) NOT NULL, CHANGE avis avis VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY id_role');
        $this->addSql('ALTER TABLE user CHANGE id_role id_role INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DC499668 FOREIGN KEY (id_role) REFERENCES role (id_role)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE avis CHANGE id_evenement id_evenement INT NOT NULL, CHANGE id_utilisateur id_utilisateur INT NOT NULL');
        $this->addSql('ALTER TABLE candidature CHANGE id_user id_user INT NOT NULL, CHANGE id_emploi id_emploi INT NOT NULL');
        $this->addSql('ALTER TABLE candidaturestage CHANGE id_user id_user INT NOT NULL, CHANGE id_stage id_stage INT NOT NULL');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FA6B3CA4B');
        $this->addSql('ALTER TABLE emploi CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT fk_user FOREIGN KEY (id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favoris CHANGE id_emploi id_emploi INT NOT NULL, CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE formation CHANGE id_formateur id_formateur INT NOT NULL');
        $this->addSql('ALTER TABLE inscription CHANGE id_formation id_formation INT NOT NULL, CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE prerequis CHANGE id_stage id_stage INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation DROP nbr_vue');
        $this->addSql('ALTER TABLE role CHANGE id_role id_role INT NOT NULL');
        $this->addSql('ALTER TABLE stage CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE suivi_reclamation ADD nbr_vue INT NOT NULL, CHANGE id_reclamation id_reclamation INT NOT NULL, CHANGE etat_reclamation etat_reclamation VARCHAR(45) DEFAULT NULL, CHANGE sujet sujet VARCHAR(255) DEFAULT NULL, CHANGE motif motif VARCHAR(255) DEFAULT NULL, CHANGE avis avis VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DC499668');
        $this->addSql('ALTER TABLE user CHANGE id_role id_role INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT id_role FOREIGN KEY (id_role) REFERENCES role (id_role) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
