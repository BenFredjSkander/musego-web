<?php

namespace App\Entity;

use App\Repository\PaymentCardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentCard
 *
 */
#[ORM\Table(name: 'payment_card')]
#[ORM\Index(columns: ['id_user'], name: 'payment_card_user_id_fk')]
#[ORM\Entity(repositoryClass: PaymentCardRepository::class)]
class PaymentCard
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
    #[ORM\Column(name: 'customer_id', type: 'string', length: 255, nullable: true)]
    private $customerId;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'type', type: 'string', length: 100, nullable: true)]
    private $type;

    /**
     * @var float|null
     *
     */
    #[ORM\Column(name: 'balance', type: 'float', precision: 10, scale: 0, nullable: true)]
    private $balance;

    /**
     * @var User
     *
     */
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: 'User')]
    private $idUser;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): self
    {
        $this->customerId = $customerId;

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

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(?float $balance): self
    {
        $this->balance = $balance;

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
