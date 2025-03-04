<?php

namespace App\Requests\ScheduleController;
use App\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

class AddScheduleItemRequest extends BaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Range(
        min: 1,
        max: 7
    )]
    protected int $dayOfWeek;

    #[Assert\NotBlank]
    #[Assert\Positive]
    protected int $subjectId;

    #[Assert\NotBlank]
    #[Assert\Time]
    protected string $startTime;
    #[Assert\NotBlank]
    #[Assert\Time]
    protected string $endTime;

    #[Assert\NotBlank]
    #[Assert\Positive]
    protected int $scheduleId;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[CustomAssert\TeacherExist]
    protected int $teacherId;
    
    protected function autoValidateRequest(): bool
    {
        return true;
    }
}