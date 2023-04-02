<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Formation
 *
 */
#[ORM\Table(name: 'formation')]
#[ORM\Index(name: 'id_formateur_idx_idx', columns: ['id_formateur'])]
#[ORM\Entity]
class Formation
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_formation', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idFormation;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'nom_formation', type: 'string', length: 45, nullable: false)]
    private $nomFormation;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'description', type: 'string', length: 45, nullable: false)]
    private $description;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'duree', type: 'integer', nullable: false)]
    private $duree;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'prix', type: 'integer', nullable: false)]
    private $prix;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_formateur', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idFormateur;

    public function getIdFormation(): ?int
    {
        return $this->idFormation;
    }

    public function getNomFormation(): ?string
    {
        return $this->nomFormation;
    }

    public function setNomFormation(string $nomFormation): self
    {
        $this->nomFormation = $nomFormation;

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

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getIdFormateur(): ?User
    {
        return $this->idFormateur;
    }

    public function setIdFormateur(?User $idFormateur): self
    {
        $this->idFormateur = $idFormateur;

        return $this;
    }


}
