<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/**
 * Reclamation
 *
 */
#[ORM\Table(name: 'reclamation')]
#[ORM\Index(name: 'id_utilisateur_idx', columns: ['id_user'])]
#[ORM\Entity]
class Reclamation
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_reclamation', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idReclamation;

    /**
     * @var \Date|null
     *
     */
    #[ORM\Column(name: 'date_reclamation', type: 'date', nullable: true)]
    private $dateReclamation;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: true)]
    private $description;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'type_reclamation', type: 'string', length: 60, nullable: true)]
    private $typeReclamation;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'email', type: 'string', length: 60, nullable: true)]
    private $email;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

    public function getIdReclamation(): ?int
    {
        return $this->idReclamation;
    }

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->dateReclamation;
    }

    public function setDateReclamation(?\DateTimeInterface $dateReclamation): self
    {
        $this->dateReclamation = $dateReclamation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeReclamation(): ?string
    {
        return $this->typeReclamation;
    }

    public function setTypeReclamation(?string $typeReclamation): self
    {
        $this->typeReclamation = $typeReclamation;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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
/**
 * @ORM\OneToMany(targetEntity=SuiviReclamation::class, mappedBy="reclamation")
 */
private $suiviReclamations;

public function __construct()
{
    $this->suiviReclamations = new ArrayCollection();
}

public function getSuiviReclamations(): Collection
{
    return $this->suiviReclamations;
}

public function addSuiviReclamation(SuiviReclamation $suiviReclamation): self
{
    if (!$this->suiviReclamations->contains($suiviReclamation)) {
        $this->suiviReclamations[] = $suiviReclamation;
        $suiviReclamation->setReclamation($this);
    }

    return $this;
}

public function removeSuiviReclamation(SuiviReclamation $suiviReclamation): self
{
    if ($this->suiviReclamations->removeElement($suiviReclamation)) {
        // set the owning side to null (unless already changed)
        if ($suiviReclamation->getReclamation() === $this) {
            $suiviReclamation->setReclamation(null);
        }
    }

    return $this;
}




}
