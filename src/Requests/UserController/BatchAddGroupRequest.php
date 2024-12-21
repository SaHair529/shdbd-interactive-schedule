<?php

namespace App\Requests\UserController;

use App\Requests\BaseRequest;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

class BatchAddGroupRequest extends BaseRequest
{
    #[Assert\NotBlank]
    protected array $usersIds;

    #[Assert\NotBlank]
    #[CustomAssert\GroupExist]
    protected int $groupId;
}