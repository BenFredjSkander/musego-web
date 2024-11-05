<?php

namespace App\Entity;

use App\Repository\ArtisteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Artiste
 *
 */
#[ORM\Table(name: 'artiste')]
#[ORM\UniqueConstraint(name: 'cin', columns: ['cin'])]
#[ORM\UniqueConstraint(name: 'image', columns: ['image'])]
#[ORM\Entity(repositoryClass: ArtisteRepository::class)]
class Artiste
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
     * @var string|null
     *
     */
    #[ORM\Column(name: 'prenom', type: 'string', length: 255, nullable: true)]
    private $prenom;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: 'cin', type: 'integer', nullable: false)]
    private $cin;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: true)]
    private $email;

    /**
     * @var \DateTime|null
     *
     */
    #[ORM\Column(name: 'date_naissance', type: 'datetime', nullable: true)]
    private $dateNaissance;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'adresse', type: 'string', length: 255, nullable: true)]
    private $adresse;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'specialite', type: 'string', length: 255, nullable: true)]
    private $specialite;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'nationalite', type: 'string', length: 50, nullable: true)]
    private $nationalite;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'image', type: 'string', length: 255, nullable: true)]
    private $image;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): self
    {
        $this->nationalite = $nationalite;

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


}
