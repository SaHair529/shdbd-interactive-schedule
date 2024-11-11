<?php

namespace App\Entity;

use App\Enum\EventType;
use App\Repository\ScheduleEventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ScheduleEventRepository::class)]
class ScheduleEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['schedule_event'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleEvents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['schedule_event'])]
    private ?User $student = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleEvents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['schedule_event'])]
    private ?ScheduleItem $scheduleItem = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['schedule_event'])]
    private ?string $reason = null;

    #[ORM\Column]
    #[Groups(['schedule_event'])]
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
