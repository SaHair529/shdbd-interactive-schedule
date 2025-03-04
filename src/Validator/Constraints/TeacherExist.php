<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TeacherExist extends Constraint
{
    public string $message = 'Teacher does not exist.';
}