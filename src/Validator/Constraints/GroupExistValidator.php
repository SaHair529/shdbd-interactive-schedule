<?php

namespace App\Validator\Constraints;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class GroupExistValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof GroupExist) {
            throw new UnexpectedTypeException($constraint, GroupExist::class);
        }

        if (null === $value || '' === $value) {
            $this->context->buildViolation('This value should not be blank.')
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
            return;
        }

        $group = $this->entityManager->getRepository(Group::class)->find($value);
        if (!$group) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}