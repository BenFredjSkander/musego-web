<?php

namespace App\Entity;

use App\Repository\PassTokenRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * PassToken
 *
 */
#[ORM\Table(name: 'pass_token')]
#[ORM\Index(columns: ['id_user'], name: 'pass_token_user_id_fk')]
#[ORM\Entity(repositoryClass: PassTokenRepository::class)]
class PassToken
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
     * @var DateTime|null
     *
     */
    #[ORM\Column(name: 'created_date', type: 'datetime', nullable: true)]
    private $createdDate;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'token', type: 'string', length: 255, nullable: true)]
    private $token;

    /**
     * @var bool|null
     *
     */
    #[ORM\Column(name: 'used', type: 'boolean', nullable: true, options: ['default' => false])]
    private bool $used = false;

    /**
     * @var User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

    public function __construct()
    {
        $this->createdDate = new DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function isUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(?bool $used): self
    {
        $this->used = $used;

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
