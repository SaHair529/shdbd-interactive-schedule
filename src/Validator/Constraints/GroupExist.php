<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class GroupExist extends Constraint
{
    public string $message = 'Group doesn`t exist';
}