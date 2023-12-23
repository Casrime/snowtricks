<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\UserRepository;
use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEntityValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @param ?string      $value
     * @param UniqueEntity $constraint
     */
    #[Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $userExists = $this->userRepository->findOneBy(['username' => $value]);

        if (null === $userExists) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
