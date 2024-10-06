<?php

namespace App\Requests\ScheduleController;

use App\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

class CreateScheduleRequest extends BaseRequest
{
    #[Assert\NotBlank]
    protected string $title;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}