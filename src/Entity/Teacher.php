<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    /**
     * @var Collection<int, ScheduleItem>
     */
    #[ORM\OneToMany(targetEntity: ScheduleItem::class, mappedBy: 'teacher')]
    private Collection $scheduleItems;

    public function __construct()
    {
        $this->scheduleItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $scheduleItem->setTeacher($this);
        }

        return $this;
    }

    public function removeScheduleItem(ScheduleItem $scheduleItem): static
    {
        if ($this->scheduleItems->removeElement($scheduleItem)) {
            // set the owning side to null (unless already changed)
            if ($scheduleItem->getTeacher() === $this) {
                $scheduleItem->setTeacher(null);
            }
        }

        return $this;
    }
}
