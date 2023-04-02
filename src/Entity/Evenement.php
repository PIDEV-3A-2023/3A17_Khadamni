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
    private $nomevenement;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'descriptionEvenement', type: 'string', length: 255, nullable: false)]
    private $descriptionevenement;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'Inviter', type: 'string', length: 255, nullable: false)]
    private $inviter;

    /**
     * @var \DateTime
     *
     */
    #[ORM\Column(name: 'dateEvenement', type: 'date', nullable: false)]
    private $dateevenement;

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
        $this->nomevenement = $nomevenement;

        return $this;
    }

    public function getDescriptionevenement(): ?string
    {
        return $this->descriptionevenement;
    }

    public function setDescriptionevenement(string $descriptionevenement): self
    {
        $this->descriptionevenement = $descriptionevenement;

        return $this;
    }

    public function getInviter(): ?string
    {
        return $this->inviter;
    }

    public function setInviter(string $inviter): self
    {
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


}
