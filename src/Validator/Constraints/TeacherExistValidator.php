<?php

namespace App\Validator\Constraints;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TeacherExistValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof TeacherExist) {
            throw new UnexpectedTypeException($constraint, ScheduleItemExist::class);
        }

        if (null === $value || '' === $value) {
            $this->context->buildViolation('This value should not be blank.')
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
            return;
        }

        $teacher = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $value]);
        
        if (!$teacher || !in_array(UserRole::ROLE_TEACHER->value, $teacher->getRoles())) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}