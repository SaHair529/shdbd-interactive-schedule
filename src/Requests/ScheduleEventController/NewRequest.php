<?php

namespace App\Requests\ScheduleEventController;

use App\Requests\BaseRequest;
use App\Validator\Constraints as CustomAssert;

use Symfony\Component\Validator\Constraints as Assert;
class NewRequest extends BaseRequest
{
    #[Assert\Positive]
    #[CustomAssert\ScheduleItemExist]
    protected int $scheduleItemId;

    #[Assert\NotBlank]
    protected string $reason;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[CustomAssert\ScheduleEventType]
    protected int $type;
}