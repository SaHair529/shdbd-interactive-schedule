<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['schedule_event', 'user'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['schedule_event', 'user'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user'])]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, AccessToken>
     */
#[ORM\OneToMany(targetEntity: AccessToken::class, mappedBy: 'owner', cascade: ['remove'], orphanRemoval: true)]
    private Collection $accessTokens;

    /**
     * @var Collection<int, Schedule>
     */
    #[ORM\ManyToMany(targetEntity: Schedule::class, mappedBy: 'musers')]
    private Collection $schedules;

    /**
     * @var Collection<int, ScheduleEvent>
     */
    #[ORM\OneToMany(targetEntity: ScheduleEvent::class, mappedBy: 'student', orphanRemoval: true)]
    private Collection $scheduleEvents;

    #[ORM\Column(length: 255)]
    #[Groups(['user'])]
    private ?string $fullName = null;

    public function __construct()
    {
        $this->accessTokens = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        $this->scheduleEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, AccessToken>
     */
    public function getAccessTokens(): Collection
    {
        return $this->accessTokens;
    }

    public function addAccessToken(AccessToken $accessToken): static
    {
        if (!$this->accessTokens->contains($accessToken)) {
            $this->accessTokens->add($accessToken);
            $accessToken->setOwner($this);
        }

        return $this;
    }

    public function removeAccessToken(AccessToken $accessToken): static
    {
        if ($this->accessTokens->removeElement($accessToken)) {
            // set the owning side to null (unless already changed)
            if ($accessToken->getOwner() === $this) {
                $accessToken->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Schedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): static
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
            $schedule->addMuser($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): static
    {
        if ($this->schedules->removeElement($schedule)) {
            $schedule->removeMuser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ScheduleEvent>
     */
    public function getScheduleEvents(): Collection
    {
        return $this->scheduleEvents;
    }

    public function addScheduleEvent(ScheduleEvent $scheduleEvent): static
    {
        if (!$this->scheduleEvents->contains($scheduleEvent)) {
            $this->scheduleEvents->add($scheduleEvent);
            $scheduleEvent->setStudent($this);
        }

        return $this;
    }

    public function removeScheduleEvent(ScheduleEvent $scheduleEvent): static
    {
        if ($this->scheduleEvents->removeElement($scheduleEvent)) {
            // set the owning side to null (unless already changed)
            if ($scheduleEvent->getStudent() === $this) {
                $scheduleEvent->setStudent(null);
            }
        }

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }
}
