<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MinAge extends Constraint
{
    public $message = 'You should be at least {{ limit }} years old.';
}