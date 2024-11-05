<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Evenement
 *
 */
#[ORM\Table(name: 'evenement')]
#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
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
    #[ORM\Column(name: 'type', type: 'string', length: 255, nullable: true)]
    private $type;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'lieu', type: 'string', length: 255, nullable: true)]
    private $lieu;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'description', type: 'text', length: 0, nullable: true)]
    private $description;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'poster', type: 'string', length: 255, nullable: true)]
    private $poster;

    /**
     * @var int|null
     *
     */
    #[ORM\Column(name: 'nb_participants', type: 'integer', nullable: true)]
    private $nbParticipants = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     */
    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'idEvenement')]
    private $idUser = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idUser = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getNbParticipants(): ?int
    {
        return $this->nbParticipants;
    }

    public function setNbParticipants(?int $nbParticipants): self
    {
        $this->nbParticipants = $nbParticipants;

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
            $idUser->addIdEvenement($this);
        }


        return $this;
    }

    public function removeIdUser(User $idUser): self
    {
        if ($this->idUser->removeElement($idUser)) {
            $idUser->removeIdEvenement($this);
        }

        return $this;
    }

    public function conatinsIdUser(User $User): bool
    {
        return ($this->idUser->contains($User));
    }


}
