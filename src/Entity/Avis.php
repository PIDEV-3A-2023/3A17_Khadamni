<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

/**
 * Avis
 *
 */
#[ORM\Table(name: 'avis')]
#[ORM\Index(name: 'fk_event', columns: ['id_evenement'])]
#[ORM\Index(name: 'fk_user_event', columns: ['id_utilisateur'])]
#[ORM\Entity]
class Avis
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_avis', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idAvis;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'note', type: 'integer', nullable: false)]
    private $note;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'commentaire', type: 'string', length: 255, nullable: false)]
    private $commentaire;

    /**
     * @var \DateTime
     *
     */
    #[ORM\Column(name: 'date_creation', type: 'date', nullable: false)]
    private $dateCreation;

    /**
     * @var \Evenement
     *
     */
    #[ORM\JoinColumn(name: 'id_evenement', referencedColumnName: 'idEvenement')]
    #[ORM\ManyToOne(targetEntity: 'Evenement')]
    private $idEvenement;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUtilisateur;

    public function getIdAvis(): ?int
    {
        return $this->idAvis;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getIdEvenement(): ?Evenement
    {
        return $this->idEvenement;
    }

    public function setIdEvenement(?Evenement $idEvenement): self
    {
        $this->idEvenement = $idEvenement;

        return $this;
    }

    public function getIdUtilisateur(): ?User
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?User $idUtilisateur): self
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }


}