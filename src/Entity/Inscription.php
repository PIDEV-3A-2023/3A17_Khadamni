<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InscriptionRepository;

/**
 * Inscription
 *
 */
#[ORM\Table(name: 'inscription')]
#[ORM\Index(name: 'id_formation_idx', columns: ['id_formation'])]
#[ORM\Index(name: 'id_user_idx', columns: ['id_user'])]
#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_inscription', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idInscription;

    /**
     * @var \DateTime
     *
     */
    #[ORM\Column(name: 'date_inscription', type: 'datetime', nullable: false)]
    private $dateInscription;

    /**
     * @var \Formation
     *
     */
    #[ORM\JoinColumn(name: 'id_formation', referencedColumnName: 'id_formation')]
    #[ORM\ManyToOne(targetEntity: 'Formation')]
    private $idFormation;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

    public function getIdInscription(): ?int
    {
        return $this->idInscription;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getIdFormation(): ?Formation
    {
        return $this->idFormation;
    }

    public function setIdFormation(?Formation $idFormation): self
    {
        $this->idFormation = $idFormation;

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
