<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ScheduleEventType extends Constraint
{
    public string $message = 'This value is not a valid event type';
}