<?php

namespace App\Entity;

use App\Enum\EventType;
use App\Repository\ScheduleEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleEventRepository::class)]
class ScheduleEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $student = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ScheduleItem $scheduleItem = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column]
    private ?EventType $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getScheduleItem(): ?ScheduleItem
    {
        return $this->scheduleItem;
    }

    public function setScheduleItem(?ScheduleItem $scheduleItem): static
    {
        $this->scheduleItem = $scheduleItem;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getType(): ?EventType
    {
        return $this->type;
    }

    public function setType(EventType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
