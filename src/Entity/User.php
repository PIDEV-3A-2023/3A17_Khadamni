<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 */
#[ORM\Table(name: 'user')]
#[ORM\Index(name: 'id_role_idx', columns: ['id_role'])]
#[ORM\UniqueConstraint(name: 'email_UNIQUE', columns: ['email'])]
#[ORM\UniqueConstraint(name: 'id_user_UNIQUE', columns: ['id_user'])]
#[ORM\Entity]
class User
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id_user', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idUser;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'nom', type: 'string', length: 45, nullable: false)]
    private $nom;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'prenom', type: 'string', length: 45, nullable: false)]
    private $prenom;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'num_tel', type: 'integer', nullable: false)]
    private $numTel;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'adresse', type: 'string', length: 45, nullable: false)]
    private $adresse;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'centre_intere', type: 'string', length: 45, nullable: true)]
    private $centreIntere;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'adresse_entreprise', type: 'string', length: 45, nullable: true)]
    private $adresseEntreprise;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'nom_entreprise', type: 'string', length: 45, nullable: true)]
    private $nomEntreprise;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'email', type: 'string', length: 45, nullable: false)]
    private $email;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'mdp', type: 'string', length: 250, nullable: false)]
    private $mdp;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'cv', type: 'string', length: 500, nullable: true)]
    private $cv;

    /**
     * @var int|null
     *
     */
    #[ORM\Column(name: 'etat_user', type: 'integer', nullable: true)]
    private $etatUser;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'age', type: 'integer', nullable: false)]
    private $age;

    /**
     * @var int|null
     *
     */
    #[ORM\Column(name: 'note', type: 'integer', nullable: true)]
    private $note;

    /**
     * @var float|null
     *
     */
    #[ORM\Column(name: 'rating', type: 'float', precision: 10, scale: 0, nullable: true)]
    private $rating;

    /**
     * @var \Role
     *
     */
    #[ORM\JoinColumn(name: 'id_role', referencedColumnName: 'id_role')]
    #[ORM\ManyToOne(targetEntity: 'Role')]
    private $idRole;

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->numTel;
    }

    public function setNumTel(int $numTel): self
    {
        $this->numTel = $numTel;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCentreIntere(): ?string
    {
        return $this->centreIntere;
    }

    public function setCentreIntere(?string $centreIntere): self
    {
        $this->centreIntere = $centreIntere;

        return $this;
    }

    public function getAdresseEntreprise(): ?string
    {
        return $this->adresseEntreprise;
    }

    public function setAdresseEntreprise(?string $adresseEntreprise): self
    {
        $this->adresseEntreprise = $adresseEntreprise;

        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(?string $nomEntreprise): self
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): self
    {
        $this->cv = $cv;

        return $this;
    }

    public function getEtatUser(): ?int
    {
        return $this->etatUser;
    }

    public function setEtatUser(?int $etatUser): self
    {
        $this->etatUser = $etatUser;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getIdRole(): ?Role
    {
        return $this->idRole;
    }

    public function setIdRole(?Role $idRole): self
    {
        $this->idRole = $idRole;

        return $this;
    }


}
