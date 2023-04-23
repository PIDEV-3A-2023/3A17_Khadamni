<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FormationRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formation
 *
 */
#[ORM\Table(name: 'formation')]
#[ORM\Index(name: 'id_formateur_idx_idx', columns: ['id_formateur'])]
#[ORM\Entity(repositoryClass: FormationRepository::class)]
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
    #[Assert\Length(min: 4,minMessage: "le nom doit contenir au moins 4 caracteres")]
    private $nomFormation;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'description', type: 'string', length: 45, nullable: false)]
    #[Assert\Length(min: 10,minMessage: "la déscription doit contenir au moins 10 caracteres")]
    private $description;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'duree', type: 'integer', nullable: false)]
    #[Assert\GreaterThanOrEqual(1,message: "la valeur doit etre supérieur à 1")]
    private $duree;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'prix', type: 'integer', nullable: false)]
    #[Assert\GreaterThanOrEqual(1,message: "la valeur doit etre supérieur à 1")]
    private $prix;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_formateur', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idFormateur;

    private $nomFormateur;
    private float $rating;

    /**
     * @return float
     */
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }


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

    public function setNomFormateur($nomFormateur): void
    {
        $this->nomFormateur = $nomFormateur;
    }

    public function getNomFormateur()
    {
        return $this->nomFormateur;
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
    public function __toString()
    {
        return $this->getNomFormation(); // or any other property that uniquely identifies the entity
    }




}
