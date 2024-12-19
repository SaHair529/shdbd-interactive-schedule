<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['schedule_with_items', 'user_schedule'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['schedule_with_items', 'user_schedule'])]
    private ?string $title = null;

    /**
     * @var Collection<int, ScheduleItem>
     */
    #[ORM\OneToMany(targetEntity: ScheduleItem::class, mappedBy: 'schedule', orphanRemoval: true)]
    #[Groups('schedule_with_items')]
    private Collection $scheduleItems;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'schedules')]
    private Collection $musers;

    #[ORM\ManyToOne(inversedBy: 'schedule')]
    private ?Group $groupp = null;

    public function __construct()
    {
        $this->scheduleItems = new ArrayCollection();
        $this->musers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, ScheduleItem>
     */
    public function getScheduleItems(): Collection
    {
        return $this->scheduleItems;
    }

    public function addScheduleItem(ScheduleItem $scheduleItem): static
    {
        if (!$this->scheduleItems->contains($scheduleItem)) {
            $this->scheduleItems->add($scheduleItem);
            $scheduleItem->setSchedule($this);
        }

        return $this;
    }

    public function removeScheduleItem(ScheduleItem $scheduleItem): static
    {
        if ($this->scheduleItems->removeElement($scheduleItem)) {
            // set the owning side to null (unless already changed)
            if ($scheduleItem->getSchedule() === $this) {
                $scheduleItem->setSchedule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMusers(): Collection
    {
        return $this->musers;
    }

    public function addMuser(User $muser): static
    {
        if (!$this->musers->contains($muser)) {
            $this->musers->add($muser);
        }

        return $this;
    }

    public function removeMuser(User $muser): static
    {
        $this->musers->removeElement($muser);

        return $this;
    }

    public function getGroupp(): ?Group
    {
        return $this->groupp;
    }

    public function setGroupp(?Group $groupp): static
    {
        $this->groupp = $groupp;

        return $this;
    }
}
