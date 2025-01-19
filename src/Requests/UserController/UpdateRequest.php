<?php

namespace App\Requests\UserController;

use App\Requests\BaseRequest;
use App\Validator\Constraints as CustomAssert;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateRequest extends BaseRequest
{
    protected string $email;

    protected string $fullName;

    #[CustomAssert\ValidUserRoles]
    protected array $roles;

    protected array $groups;
}