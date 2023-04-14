<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prerequis
 *
 */
#[ORM\Table(name: 'prerequis')]
#[ORM\Index(name: 'id_stage', columns: ['id_stage'])]
#[ORM\Entity]
class Prerequis
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_prerequis', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idPrerequis;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'niveau_etude', type: 'string', length: 0, nullable: false)]
    private $niveauEtude;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'description', type: 'string', length: 200, nullable: false)]
    private $description;

    /**
     * @var \Stage
     *
     */
    #[ORM\JoinColumn(name: 'id_stage', referencedColumnName: 'id_stage')]
    #[ORM\ManyToOne(targetEntity: 'Stage')]
    private $idStage;

    public function getIdPrerequis(): ?int
    {
        return $this->idPrerequis;
    }

    public function getNiveauEtude(): ?string
    {
        return $this->niveauEtude;
    }

    public function setNiveauEtude(string $niveauEtude): self
    {
        $this->niveauEtude = $niveauEtude;

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
