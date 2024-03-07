<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class MinAgeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof MinAge) {
            throw new UnexpectedTypeException($constraint, MinAge::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        // Calculate age based on the provided date of birth
        $now = new \DateTime();
        $age = $now->diff($value)->y;

        if ($age < 13) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ limit }}', 13)
                ->addViolation();
        }
    }
}