<?php

namespace App\Requests\ScheduleController;
use App\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

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
    protected function autoValidateRequest(): bool
    {
        return true;
    }
}