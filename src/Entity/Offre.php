<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Offre
 *
 */
#[ORM\Table(name: 'offre')]
#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
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
    #[ORM\Column(name: 'type', type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez spécifier un type")]
    private $type;

    /**
     * @var int|null
     *
     */
    #[ORM\Column(name: 'prix', type: 'integer', nullable: true)]
    #[Assert\NotBlank(message: "Veuillez spécifier un prix")]
    private $prix;

    /**
     * @var int|null
     *
     */
    #[ORM\Column(name: 'promotion', type: 'integer', nullable: true)]
    #[Assert\NotBlank(message: "Veuillez spécifier une promotion")]
    private $promotion;

    /**
     * @var DateTime|null
     *
     */
    #[ORM\Column(name: 'date_debut', type: 'datetime', nullable: true)]
    #[Assert\NotBlank(message: "Veuillez spécifier une date début")]
    private $dateDebut;

    /**
     * @var DateTime|null
     *
     */
    #[ORM\Column(name: 'date_fin', type: 'datetime', nullable: true)]
    #[Assert\NotBlank(message: "Veuillez spécifier une date fin")]
    private $dateFin;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'description', type: 'text', length: 0, nullable: true)]
    private $description;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPromotion(): ?int
    {
        return $this->promotion;
    }

    public function setPromotion(?int $promotion): self
    {
        $this->promotion = $promotion;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
