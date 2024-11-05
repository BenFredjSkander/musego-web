<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Abonnement
 *
 */
#[ORM\Table(name: 'abonnement')]
#[ORM\Index(columns: ['id_offre'], name: 'abonnement_offre_id_fk')]
#[ORM\Index(columns: ['id_user'], name: 'abonnement_user_id_fk')]
#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
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
     * @var int|null
     *
     */
    #[ORM\Column(name: 'prix', type: 'integer', nullable: true)]
    private $prix;

    /**
     * @var Offre
     *
     */
    #[ORM\JoinColumn(name: 'id_offre', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: 'Offre')]
    private $idOffre;

    /**
     * @var User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: "id", nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    private User $idUser;

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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getIdOffre(): ?Offre
    {
        return $this->idOffre;
    }

    public function setIdOffre(?Offre $idOffre): self
    {
        $this->idOffre = $idOffre;

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
