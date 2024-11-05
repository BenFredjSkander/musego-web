<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(name: 'id', type: 'bigint', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(message: 'Email invalid')]
    #[Assert\NotBlank(message: 'The fields {{ label }} are missing.')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var DateTime|null
     *
     */
    #[ORM\Column(name: 'created_date', type: 'datetime', nullable: true, updatable: false)]
    private ?DateTime $createdDate;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: 'username', type: 'string', length: 60, nullable: false)]
    #[Assert\NotBlank(message: 'The fields {{ label }} are missing.')]
    private string $username;


    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'profile_pic', type: 'string', length: 255, nullable: true)]
    private ?string $profilePic;

    /**
     * @var DateTime|null
     *
     */
    #[ORM\Column(name: 'birthdate', type: 'datetime', nullable: true)]
    private ?DateTime $birthdate;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'phone_number', type: 'string', length: 30, nullable: true)]
    #[Assert\NotBlank(message: 'The fields {{ label }} are missing.')]
    #[Assert\Regex(pattern: "/^[0-9,*,(,), ,-]*$/", message: "Please provide a valid phone number.")]
    private ?string $phoneNumber;

    /**
     * @var string|null
     *
     */
    #[ORM\Column(name: 'speciality', type: 'string', length: 255, nullable: true)]
    private ?string $speciality;

    /**
     * @var DateTime|null
     *
     */
    #[ORM\Column(name: 'hiring_date', type: 'datetime', nullable: true)]
    private ?DateTime $hiringDate;


    /**
     * @var bool|null
     *
     */
    #[ORM\Column(name: 'locked', type: 'boolean', nullable: true, options: ['default' => false])]
    private ?bool $locked = false;

    /**
     * @var Collection
     *
     */
    #[ORM\JoinTable(name: 'participation')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'id_evenement', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'Evenement', inversedBy: 'idUser')]
    private $idEvenement = array();

    /**
     * @var Collection
     *
     */
    #[ORM\JoinTable(name: 'attendance')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'id_formation', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'Formation', inversedBy: 'idUser')]
    private $idFormation = array();

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'idUser', targetEntity: Abonnement::class, orphanRemoval: true)]
    #[Ignore]
    private Collection $abonnements;

    #[ORM\OneToOne(mappedBy: 'idUser', targetEntity: Abonnement::class, orphanRemoval: true)]
    #[Ignore]
    private Abonnement $abonnement;

    #[ORM\OneToMany(mappedBy: 'idUser', targetEntity: Session::class, orphanRemoval: true)]
    private Collection $sessions;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $lastActiveAt = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idEvenement = new ArrayCollection();
        $this->idFormation = new ArrayCollection();
        $this->createdDate = new DateTime();
        $this->lastActiveAt = new DateTime();
        $this->abonnements = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return $this->profilePic;
    }

    public function setProfilePic(?string $profilePic): self
    {
        $this->profilePic = $profilePic;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(?string $speciality): self
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function getHiringDate(): ?\DateTimeInterface
    {
        return $this->hiringDate;
    }

    public function setHiringDate(?\DateTimeInterface $hiringDate): self
    {
        $this->hiringDate = $hiringDate;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(?bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }


    /**
     * @return Collection<int, Evenement>
     */
    public function getIdEvenement(): Collection
    {
        return $this->idEvenement;
    }

    public function addIdEvenement(Evenement $idEvenement): self
    {
        if (!$this->idEvenement->contains($idEvenement)) {
            $this->idEvenement->add($idEvenement);
        }

        return $this;
    }

    public function removeIdEvenement(Evenement $idEvenement): self
    {
        $this->idEvenement->removeElement($idEvenement);

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getIdFormation(): Collection
    {
        return $this->idFormation;
    }

    public function addIdFormation(Formation $idFormation): self
    {
        if (!$this->idFormation->contains($idFormation)) {
            $this->idFormation->add($idFormation);
        }

        return $this;
    }

    public function removeIdFormation(Formation $idFormation): self
    {
        $this->idFormation->removeElement($idFormation);

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getLastActiveAt(): ?DateTime
    {
        return $this->lastActiveAt;
    }

    public function setLastActiveAt(?\DateTime $lastActiveAt): self
    {
        $this->lastActiveAt = $lastActiveAt;

        return $this;
    }

    /**
     * @return Bool Whether the user is active or not
     */
    public function isActiveNow(): bool
    {
        // Delay during wich the user will be considered as still active
        $delay = new \DateTime('2 minutes ago');

        return ($this->getLastActiveAt() > $delay);
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function getAbonnement(): Abonnement|null
    {
        if(isset($this->abonnement)) return $this->abonnement;
        return null;
    }

    public function addAbonnement(Abonnement $abonnement): self
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->setIdUser($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): self
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getIdUser() === $this) {
                $abonnement->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setIdUser($this);
        }

        return $this;
    }

    public function removeSession(Session $abonnement): self
    {
        $this->abonnements->removeElement($abonnement);


        return $this;
    }
}
