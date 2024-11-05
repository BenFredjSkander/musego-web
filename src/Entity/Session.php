<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 *
 */
#[ORM\Table(name: 'session')]
#[ORM\Index(columns: ['id_user'], name: 'session_user_id_fk')]
#[ORM\UniqueConstraint(name: 'session', columns: ['uid', 'id_user'])]
#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
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
     * @var DateTime|null
     *
     */
    #[ORM\Column(name: 'expiry', type: 'datetime', nullable: true)]
    private $expiry;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'device', type: 'string', length: 255, nullable: true)]
    private $device;

    /**
     * @var User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private User $idUser;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $browser = null;

    #[ORM\Column(length: 255)]
    private ?string $uid = null;

    /**
     * @param string|null $device
     * @param string|null $browser
     * @param User $idUser
     * @param string|null $uid
     */
    public function __construct(?string $device, ?string $browser, User $idUser, ?string $uid)
    {
        $this->createdDate = new DateTime();
        $this->device = $device;
        $this->idUser = $idUser;
        $this->browser = $browser;
        $this->uid = $uid;
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

    public function getExpiry(): ?\DateTimeInterface
    {
        return $this->expiry;
    }

    public function setExpiry(?\DateTimeInterface $expiry): self
    {
        $this->expiry = $expiry;

        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): self
    {
        $this->device = $device;

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

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(?string $browser): self
    {
        $this->browser = $browser;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }


}
