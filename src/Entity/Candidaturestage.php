<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Candidaturestage
 *
 */
#[ORM\Table(name: 'candidaturestage')]
#[ORM\Index(name: 'id_user', columns: ['id_user'])]
#[ORM\Index(name: 'id_stage', columns: ['id_stage'])]
#[ORM\Entity]
class Candidaturestage
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
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

    /**
     * @var \Stage
     *
     */
    #[ORM\JoinColumn(name: 'id_stage', referencedColumnName: 'id_stage')]
    #[ORM\ManyToOne(targetEntity: 'Stage')]
    private $idStage;

    public function getIdCandidature(): ?int
    {
        return $this->idCandidature;
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

    public function getIdStage(): ?Stage
    {
        return $this->idStage;
    }

    public function setIdStage(?Stage $idStage): self
    {
        $this->idStage = $idStage;

        return $this;
    }


}
