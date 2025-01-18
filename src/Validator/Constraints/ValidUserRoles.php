<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidUserRoles extends Constraint
{
    public string $message = 'This value is not a valid role';
}