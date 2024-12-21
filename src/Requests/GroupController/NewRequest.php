<?php

namespace App\Requests\GroupController;

use App\Requests\BaseRequest;

use Symfony\Component\Validator\Constraints as Assert;

class NewRequest extends BaseRequest
{
    #[Assert\NotBlank]
    protected string $name;
}