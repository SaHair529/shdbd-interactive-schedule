<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var Collection<int, ScheduleItem>
     */
    #[ORM\OneToMany(targetEntity: ScheduleItem::class, mappedBy: 'schedule', orphanRemoval: true)]
    private Collection $scheduleItems;

    public function __construct()
    {
        $this->scheduleItems = new ArrayCollection();
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
}
