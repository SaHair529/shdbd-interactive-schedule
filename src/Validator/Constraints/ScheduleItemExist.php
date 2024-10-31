<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ScheduleItemExist extends Constraint
{
    public string $message = 'This schedule item does not exist.';
}