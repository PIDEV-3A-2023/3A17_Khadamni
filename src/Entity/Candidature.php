<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Candidature
 *
 */
#[ORM\Table(name: 'candidature')]
#[ORM\Index(name: 'FK_idemploi_idx', columns: ['id_emploi'])]
#[ORM\Index(name: 'FK_iduser_idx', columns: ['id_user'])]
#[ORM\Entity]
class Candidature
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_candidature', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idCandidature;

    /**
     * @var \DateTime
     *
     */
    #[ORM\Column(name: 'date', type: 'date', nullable: false)]
    private $date;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

    /**
     * @var \Emploi
     *
     */
    #[ORM\JoinColumn(name: 'id_emploi', referencedColumnName: 'id_emploi')]
    #[ORM\ManyToOne(targetEntity: 'Emploi')]
    private $idEmploi;

    public function getIdCandidature(): ?int
    {
        return $this->idCandidature;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getIdEmploi(): ?Emploi
    {
        return $this->idEmploi;
    }

    public function setIdEmploi(?Emploi $idEmploi): self
    {
        $this->idEmploi = $idEmploi;

        return $this;
    }


}
