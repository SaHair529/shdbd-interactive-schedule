<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EmailAvailable extends Constraint
{
    public string $message = 'Email is not available';
}