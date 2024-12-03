<?php

namespace App\Validator\Constraints;

use App\Enum\UserRole;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidUserRoleValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidUserRole) {
            throw new UnexpectedTypeException($constraint, ValidUserRole::class);
        }

        if (UserRole::tryFrom($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
        }
    }
}