<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Emploi
 *
 */
#[ORM\Table(name: 'emploi')]
#[ORM\Index(name: 'fk_user_idx', columns: ['id_user'])]
#[ORM\Entity]
class Emploi
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_emploi', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idEmploi;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'titre', type: 'string', length: 45, nullable: false)]
    private $titre;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'description', type: 'string', length: 45, nullable: false)]
    private $description;

    /**
     * @var \DateTime
     *
     */
    #[ORM\Column(name: 'date_publication', type: 'date', nullable: false)]
    private $datePublication;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'salaire', type: 'integer', nullable: false)]
    private $salaire;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'niveau_experience', type: 'string', length: 0, nullable: false)]
    private $niveauExperience;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'type_contrat', type: 'string', length: 0, nullable: false)]
    private $typeContrat;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

    public function getIdEmploi(): ?int
    {
        return $this->idEmploi;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): self
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getSalaire(): ?int
    {
        return $this->salaire;
    }

    public function setSalaire(int $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getNiveauExperience(): ?string
    {
        return $this->niveauExperience;
    }

    public function setNiveauExperience(string $niveauExperience): self
    {
        $this->niveauExperience = $niveauExperience;

        return $this;
    }

    public function getTypeContrat(): ?string
    {
        return $this->typeContrat;
    }

    public function setTypeContrat(string $typeContrat): self
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }
}
