<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Favoris
 *
 */
#[ORM\Table(name: 'favoris')]
#[ORM\Index(name: 'fk_user_f', columns: ['id_user'])]
#[ORM\Index(name: 'fk_emploi_f', columns: ['id_emploi'])]
#[ORM\Entity]
class Favoris
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_favoris', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idFavoris;

    /**
     * @var \Emploi
     *
     */
    #[ORM\JoinColumn(name: 'id_emploi', referencedColumnName: 'id_emploi')]
    #[ORM\ManyToOne(targetEntity: 'Emploi')]
    private $idEmploi;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

    public function getIdFavoris(): ?int
    {
        return $this->idFavoris;
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
