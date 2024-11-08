<?php

namespace App\Validator\Constraints;

use App\Entity\ScheduleItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ScheduleItemExistValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof ScheduleItemExist) {
            throw new UnexpectedTypeException($constraint, ScheduleItemExist::class);
        }

        if (null === $value || '' === $value) {
            $this->context->buildViolation('This value should not be blank.')
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
            return;
        }

        $scheduleItem = $this->entityManager->getRepository(ScheduleItem::class)->find($value);
        if (!$scheduleItem) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}