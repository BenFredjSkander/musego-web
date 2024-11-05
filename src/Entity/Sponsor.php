<?php

namespace App\Entity;

use App\Repository\SponsorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Sponsor
 *
 */
#[ORM\Table(name: 'sponsor')]
#[ORM\Index(columns: ['id_evenement'], name: 'sponsor_evenement_id_fk')]
#[ORM\Entity(repositoryClass: SponsorRepository::class)]
class Sponsor
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
    #[ORM\Column(name: 'photo', type: 'string', length: 255, nullable: true)]
    private $photo;

    /**
     * @var int|null
     *
     */
    #[ORM\Column(name: 'capacite_fin', type: 'integer', nullable: true)]
    private $capaciteFin;

    /**
     * @var Evenement
     *
     */
    #[ORM\JoinColumn(name: 'id_evenement', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: 'Evenement')]
    private $idEvenement;

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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCapaciteFin(): ?int
    {
        return $this->capaciteFin;
    }

    public function setCapaciteFin(?int $capaciteFin): self
    {
        $this->capaciteFin = $capaciteFin;

        return $this;
    }

    public function getIdEvenement(): ?Evenement
    {
        return $this->idEvenement;
    }

    public function setIdEvenement(?Evenement $idEvenement): self
    {
        $this->idEvenement = $idEvenement;

        return $this;
    }


}
