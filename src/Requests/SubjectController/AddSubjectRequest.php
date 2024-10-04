<?php

namespace App\Requests\SubjectController;
use App\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

class AddSubjectRequest extends BaseRequest
{
    #[Assert\NotBlank]
    protected string $name;
    protected function autoValidateRequest(): bool
    {
        return true;
    }
}