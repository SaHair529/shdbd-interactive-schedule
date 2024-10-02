<?php

namespace App\Requests\ScheduleController;

use App\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

class PatchScheduleItemRequest extends BaseRequest
{
    #[Assert\Range(
        min: 1,
        max: 7
    )]
    protected int $dayOfWeek;

    #[Assert\Positive]
    protected int $subjectId;

    #[Assert\Time]
    protected string $startTime;
    #[Assert\Time]
    protected string $endTime;
    protected function autoValidateRequest(): bool
    {
        return true;
    }
}