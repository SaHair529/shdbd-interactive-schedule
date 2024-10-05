<?php

namespace App\Entity;

use App\Repository\ScheduleItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScheduleItemRepository::class)]
class ScheduleItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('schedule_item')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 7)]
    #[Groups('schedule_item')]
    private ?int $dayOfWeek = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups('schedule_item')]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups('schedule_item')]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('schedule_item')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleItems')]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Schedule $schedule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): static
    {
        $this->schedule = $schedule;

        return $this;
    }
}
