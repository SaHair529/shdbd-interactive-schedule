<?php

namespace App\Requests\UserController;

use App\Requests\BaseRequest;
use App\Validator\Constraints as CustomAssert;

use Symfony\Component\Validator\Constraints as Assert;

class NewRequest extends BaseRequest
{
    #[Assert\NotBlank]
    protected string $email;

    #[Assert\NotBlank]
    protected string $password;

    #[Assert\NotBlank]
    protected string $fullName;

    #[Assert\NotBlank]
    #[CustomAssert\ValidUserRole]
    protected string $role;
}