<?php

namespace App\Validator\Constraints;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EmailAvailableValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EmailAvailable) {
            throw new UnexpectedTypeException($constraint, EmailAvailable::class);
        }

        if (null === $value || '' === $value) {
            $this->context->buildViolation('This value should not be blank.')
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
            return;
        }
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $value]);
        if ($user) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}