<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidUserRole extends Constraint
{
    public string $message = 'This value is not a valid role';
}