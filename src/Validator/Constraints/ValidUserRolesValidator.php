<?php

namespace App\Validator\Constraints;

use App\Enum\UserRole;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidUserRolesValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidUserRoles) {
            throw new UnexpectedTypeException($constraint, ValidUserRoles::class);
        }

        if (!$value) {
            return;
        }

        foreach ($value as $role) {
            if (UserRole::tryFrom($role) === null) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', (string) $role)
                    ->addViolation();
                return;
            }
        }
    }
}