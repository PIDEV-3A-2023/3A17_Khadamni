<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Evenement
 *
 */
#[ORM\Table(name: 'evenement')]
#[ORM\Entity]
class Evenement
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'idEvenement', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idevenement;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'nomEvenement', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Nom ne doit pas être vide')]
    #[Assert\Length(min: 2,minMessage: "Le nom doit contenir au moins 2 caractères")]
    private $nomevenement;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'descriptionEvenement', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'La description ne doit pas être vide')]
    #[Assert\Length(min: 10,minMessage: "La description doit contenir au moins 10 caractères")]
    private $descriptionevenement;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'Inviter', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Le champ Invité ne doit pas être vide')]
    #[Assert\Length(min: 3,minMessage: "Le nom de l'invité doit contenir au moins 3 caractères")]
    private $inviter;

    /**
     * @var \DateTime
     *
     */
    #[ORM\Column(name: 'dateEvenement', type: 'date', nullable: false)]
    #[Assert\NotBlank(message: 'La date ne doit pas être vide')]
    private $dateevenement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\NotBlank(message: 'Image ne doit pas être vide')]
    private $imageFilename;

    public function getIdevenement(): ?int
    {
        return $this->idevenement;
    }

    public function getNomevenement(): ?string
    {
        return $this->nomevenement;
    }

    public function setNomevenement(string $nomevenement): self
    {
        $nomEvenement = trim($nomevenement);

        if (empty($nomEvenement)) {
            throw new \InvalidArgumentException('Le nom de l\'événement ne peut pas être vide.');
        }

        if (strlen($nomEvenement) > 255) {
            throw new \LengthException('Le nom de l\'événement ne peut pas dépasser 255 caractères.');
        }

        $this->nomevenement = $nomEvenement;

        return $this;
    }

    public function getDescriptionevenement(): ?string
    {
        return $this->descriptionevenement;
    }

    public function setDescriptionevenement(string $descriptionevenement): self
    {
        $descriptionEvenement = trim($descriptionevenement);

        if (empty($descriptionEvenement)) {
            throw new \InvalidArgumentException('La description de l\'événement ne peut pas être vide.');
        }

        if (strlen($descriptionEvenement) > 255) {
            throw new \LengthException('La description de l\'événement ne peut pas dépasser 255 caractères.');
        }

        $this->descriptionevenement = $descriptionEvenement;

        return $this;
    }

    public function getInviter(): ?string
    {
        return $this->inviter;
    }

    public function setInviter(string $inviter): self
    {
        $inviter = trim($inviter);

        if (empty($inviter)) {
            throw new \InvalidArgumentException('Le nom de l\'invité ne peut pas être vide.');
        }

        if (strlen($inviter) > 255) {
            throw new \LengthException('Le nom de l\'invité ne peut pas dépasser 255 caractères.');
        }

        $this->inviter = $inviter;

        return $this;
    }

    public function getDateevenement(): ?\DateTimeInterface
    {
        return $this->dateevenement;
    }

    public function setDateevenement(\DateTimeInterface $dateevenement): self
    {
        $this->dateevenement = $dateevenement;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }


}
