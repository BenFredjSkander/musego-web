<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Formation
 *
 */
#[ORM\Table(name: 'formation')]
#[ORM\Index(columns: ['id_formateur'], name: 'formation_user_id_fk')]
#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'id', type: 'bigint', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'nom', type: 'string', length: 255, nullable: true)]
    private $nom;


    /**
     * @var \DateTime|null
     *
     */
    #[ORM\Column(name: 'date_debut', type: 'datetime', nullable: true)]
    private $dateDebut;

    /**
     * @var \DateTime|null
     *
     */
    #[ORM\Column(name: 'date_fin', type: 'datetime', nullable: true)]
    private $dateFin;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'niveau', type: 'string', length: 50, nullable: true)]
    private $niveau;

    /**
     * @var User
     *
     */
    #[ORM\JoinColumn(name: 'id_formateur', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idFormateur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     */
    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'idFormation')]
    private $idUser = array();

    #[ORM\ManyToOne(inversedBy: 'formations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Atelier $atelier = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idUser = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }


    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(?string $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getIdFormateur(): ?User
    {
        return $this->idFormateur;
    }

    public function setIdFormateur(?User $idFormateur): self
    {
        $this->idFormateur = $idFormateur;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getIdUser(): Collection
    {
        return $this->idUser;
    }

    public function addIdUser(User $idUser): self
    {
        if (!$this->idUser->contains($idUser)) {
            $this->idUser->add($idUser);
            $idUser->addIdFormation($this);
        }

        return $this;
    }

    public function removeIdUser(User $idUser): self
    {
        if ($this->idUser->removeElement($idUser)) {
            $idUser->removeIdFormation($this);
        }

        return $this;
    }

    public function getAtelier(): ?Atelier
    {
        return $this->atelier;
    }

    public function setAtelier(?Atelier $atelier): self
    {
        $this->atelier = $atelier;

        return $this;
    }

    public function conatinsIdUser(User $User): bool
    {
        return ($this->idUser->contains($User));
    }

}
