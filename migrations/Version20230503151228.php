<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230503151228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('ALTER TABLE avis CHANGE id_utilisateur id_utilisateur INT DEFAULT NULL, CHANGE id_evenement id_evenement INT DEFAULT NULL');
        $this->addSql('ALTER TABLE candidature CHANGE id_user id_user INT DEFAULT NULL, CHANGE id_emploi id_emploi INT DEFAULT NULL');
        $this->addSql('ALTER TABLE candidaturestage CHANGE id_user id_user INT DEFAULT NULL, CHANGE id_stage id_stage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY fk_user');
        $this->addSql('ALTER TABLE emploi CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE evenement DROP vue');
        $this->addSql('ALTER TABLE favoris CHANGE id_emploi id_emploi INT DEFAULT NULL, CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE formation CHANGE id_formateur id_formateur INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription CHANGE id_formation id_formation INT DEFAULT NULL, CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prerequis CHANGE id_stage id_stage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE role CHANGE id_role id_role INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE stage CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE suivi_reclamation CHANGE id_reclamation id_reclamation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY id_role');
        $this->addSql('ALTER TABLE user CHANGE id_role id_role INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DC499668 FOREIGN KEY (id_role) REFERENCES role (id_role)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT AUTO_INCREMENT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rating (id INT NOT NULL, idformation_id INT NOT NULL, iduser_id INT NOT NULL, note INT NOT NULL, INDEX IDX_D8892622786A81FB (iduser_id), INDEX IDX_D889262214AF5727 (idformation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, hashed_token VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\') DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE avis CHANGE id_evenement id_evenement INT NOT NULL, CHANGE id_utilisateur id_utilisateur INT NOT NULL');
        $this->addSql('ALTER TABLE candidature CHANGE id_user id_user INT NOT NULL, CHANGE id_emploi id_emploi INT NOT NULL');
        $this->addSql('ALTER TABLE candidaturestage CHANGE id_user id_user INT NOT NULL, CHANGE id_stage id_stage INT NOT NULL');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FA6B3CA4B');
        $this->addSql('ALTER TABLE emploi CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT fk_user FOREIGN KEY (id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement ADD vue INT NOT NULL');
        $this->addSql('ALTER TABLE favoris CHANGE id_emploi id_emploi INT NOT NULL, CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE formation CHANGE id_formateur id_formateur INT NOT NULL');
        $this->addSql('ALTER TABLE inscription CHANGE id_formation id_formation INT NOT NULL, CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE prerequis CHANGE id_stage id_stage INT NOT NULL');
        $this->addSql('ALTER TABLE role CHANGE id_role id_role INT NOT NULL');
        $this->addSql('ALTER TABLE stage CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE suivi_reclamation CHANGE id_reclamation id_reclamation INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DC499668');
        $this->addSql('ALTER TABLE user CHANGE id_role id_role INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT id_role FOREIGN KEY (id_role) REFERENCES role (id_role) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
