<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SuiviReclamation
 *
 */
#[ORM\Table(name: 'suivi_reclamation')]
#[ORM\Index(name: 'fk_rec', columns: ['id_reclamation'])]
#[ORM\Entity]
class SuiviReclamation
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_suivi', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idSuivi;

  /**
 * @var string
 *
 */
#[ORM\Column(name: 'etat_reclamation', type: 'string', length: 45, nullable: false)]
private $etatReclamation ;


    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'sujet', type: 'string', length: 255, nullable: false)]
    private $sujet;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'motif', type: 'string', length: 255, nullable: false)]
    private $motif;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'avis', type: 'string', nullable: false)]
    private $avis;

    /**
     * @var \Reclamation
     *
     */
    #[ORM\JoinColumn(name: 'id_reclamation', referencedColumnName: 'id_reclamation')]
    #[ORM\ManyToOne(targetEntity: 'Reclamation')]
    private $idReclamation;
   

    public function getIdSuivi(): ?int
    {
        return $this->idSuivi;
    }

    public function getEtatReclamation(): ?string
    {
        return $this->etatReclamation;
    }

    public function setEtatReclamation(string $etatReclamation): self
    {
        $this->etatReclamation = $etatReclamation;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getAvis(): ?string
    {
        return $this->avis;
    }

    public function setAvis(string $avis): self
    {
        $this->avis = $avis;

        return $this;
    }

    public function getIdReclamation(): ?Reclamation
    {
        return $this->idReclamation;
    }

    public function setIdReclamation(?Reclamation $idReclamation): self
    {
        $this->idReclamation = $idReclamation;

        return $this;
    }

/**
 * @ORM\ManyToOne(targetEntity=Reclamation::class, inversedBy="suiviReclamations")
 * @ORM\JoinColumn(nullable=false)
 */
private $reclamation;

public function getReclamation(): ?Reclamation
{
    return $this->reclamation;
}

public function setReclamation(?Reclamation $reclamation): self
{
    $this->reclamation = $reclamation;

    return $this;
}


/**
 * @ORM\ManyToOne(targetEntity=Reclamation::class, inversedBy="suiviReclamations")
 * @ORM\JoinColumn(name="id_reclamation", referencedColumnName="id_reclamation", nullable=false)
 */



}
