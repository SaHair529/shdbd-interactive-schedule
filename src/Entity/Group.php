<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['group_list'])]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groups')]
    private Collection $participants;

    /**
     * @var Collection<int, Schedule>
     */
    #[ORM\OneToMany(targetEntity: Schedule::class, mappedBy: 'groupp')]
    private Collection $schedules;

    #[ORM\Column(length: 255)]
    #[Groups(['group_list'])]
    private ?string $name = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->schedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addGroup($this);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getGroups()->contains($this)) {
                $participant->removeGroup($this);
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

    public function addSchedules(Schedule $schedules): static
    {
        if (!$this->schedules->contains($schedules)) {
            $this->schedules->add($schedules);
            $schedules->setGroupp($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedules): static
    {
        if ($this->schedules->removeElement($schedules)) {
            // set the owning side to null (unless already changed)
            if ($schedules->getGroupp() === $this) {
                $schedules->setGroupp(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
