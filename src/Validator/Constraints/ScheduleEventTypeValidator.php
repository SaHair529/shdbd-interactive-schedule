<?php

namespace App\Validator\Constraints;

use App\Enum\EventType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ScheduleEventTypeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ScheduleEventType) {
            throw new UnexpectedTypeException($constraint, ScheduleEventType::class);
        }

        if (EventType::tryFrom($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
        }
    }
}