<?php

namespace App\Entity;

use App\Repository\OeuvreRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Oeuvre
 *
 */
#[ORM\Table(name: 'oeuvre')]
#[ORM\Index(columns: ['id_artiste'], name: 'oeuvre_artiste_id_fk')]
#[ORM\UniqueConstraint(name: 'image', columns: ['image'])]
#[ORM\Entity(repositoryClass: OeuvreRepository::class)]
class Oeuvre
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
    #[ORM\Column(name: 'titre', type: 'string', length: 255, nullable: true)]
    private $titre;

    /**
     * @var DateTime|null
     *
     */
    #[ORM\Column(name: 'date_creation', type: 'datetime', nullable: true)]
    private $dateCreation;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'categorie', type: 'string', length: 255, nullable: true)]
    private $categorie;

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
    #[ORM\Column(name: 'lieu_conservation', type: 'string', length: 255, nullable: true)]
    private $lieuConservation;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'image', type: 'string', length: 255, nullable: true)]
    private $image;

    /**
     * @var Artiste
     *
     */

    #[ORM\JoinColumn(name: 'id_artiste', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: 'Artiste')]
    private $idArtiste;


    #[ORM\JoinColumn(name: 'id_categorie', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: 'Categorie')]
    private $idCategorie;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;

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

    public function getLieuConservation(): ?string
    {
        return $this->lieuConservation;
    }

    public function setLieuConservation(?string $lieuConservation): self
    {
        $this->lieuConservation = $lieuConservation;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getIdArtiste(): ?Artiste
    {
        return $this->idArtiste;
    }

    public function setIdArtiste(?Artiste $idArtiste): self
    {
        $this->idArtiste = $idArtiste;

        return $this;
    }

    public function getIdCategorie(): ?Categorie
    {
        return $this->idCategorie;
    }

    public function setIdCategorie(?Categorie $idCategorie): self
    {
        $this->idCategorie = $idCategorie;

        return $this;
    }


}
