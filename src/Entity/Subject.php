<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['schedule_with_items', 'subject'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['schedule_with_items', 'subject'])]
    private ?string $name = null;

    /**
     * @var Collection<int, ScheduleItem>
     */
    #[ORM\OneToMany(targetEntity: ScheduleItem::class, mappedBy: 'subject', cascade: ['remove'], orphanRemoval: true)]
    private Collection $scheduleItems;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'subjects')]
    private Collection $teachers;

    public function __construct()
    {
        $this->scheduleItems = new ArrayCollection();
        $this->teachers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $scheduleItem->setSubject($this);
        }

        return $this;
    }

    public function removeScheduleItem(ScheduleItem $scheduleItem): static
    {
        if ($this->scheduleItems->removeElement($scheduleItem)) {
            // set the owning side to null (unless already changed)
            if ($scheduleItem->getSubject() === $this) {
                $scheduleItem->setSubject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(User $teacher): static
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers->add($teacher);
        }

        return $this;
    }

    public function removeTeacher(User $teacher): static
    {
        $this->teachers->removeElement($teacher);

        return $this;
    }
}
