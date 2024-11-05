<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Avis
 *
 */
#[ORM\Table(name: 'avis')]
#[ORM\Index(columns: ['id_user'], name: 'id_user')]
#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
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
    #[Assert\NotBlank(message: "type ne peux pas être vide! ")]
    
    private $type;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'description', type: 'text', length: 255, nullable: true)]
    #[Assert\NotBlank(message: "description ne peux pas être vide! ")]
    #[Assert\Length(
        min: 7,
        max: 20,
        minMessage: "The description must be at least {{ limit }} characters long",
        maxMessage: "The descriptiion cannot be longer than {{ limit }} characters"
    )]
    private $description;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'avis_sur', type: 'string', length: 255, nullable: true)]
    private $avisSur;

    /**
     * @var \User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAvisSur(): ?string
    {
        return $this->avisSur;
    }

    public function setAvisSur(?string $avisSur): self
    {
        $this->avisSur = $avisSur;

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
