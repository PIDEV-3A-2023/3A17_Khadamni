<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stage
 *
 */
#[ORM\Table(name: 'stage')]
#[ORM\Index(name: 'id_user', columns: ['id_user'])]
#[ORM\Entity]
class Stage
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_stage', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idStage;
    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'poste', type: 'string', length: 50, nullable: false)]
    private $poste;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'nom_entreprise', type: 'string', length: 50, nullable: false)]
    private $nomEntreprise;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'adresse_stage', type: 'string', length: 50, nullable: false)]
    private $adresseStage;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'duree_stage', type: 'string', length: 50, nullable: false)]
    private $dureeStage;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'type_stage', type: 'string', length: 0, nullable: false)]
    private $typeStage;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

    public function getIdStage(): ?int
    {
        return $this->idStage;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(string $nomEntreprise): self
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getAdresseStage(): ?string
    {
        return $this->adresseStage;
    }

    public function setAdresseStage(string $adresseStage): self
    {
        $this->adresseStage = $adresseStage;

        return $this;
    }

    public function getDureeStage(): ?string
    {
        return $this->dureeStage;
    }

    public function setDureeStage(string $dureeStage): self
    {
        $this->dureeStage = $dureeStage;

        return $this;
    }

    public function getTypeStage(): ?string
    {
        return $this->typeStage;
    }

    public function setTypeStage(string $typeStage): self
    {
        $this->typeStage = $typeStage;

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
