<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Engine\Constraint;
use Symfony\Components\Validator\Engine\ConstraintValidator;

class Boolean extends Constraint
{
  public $message = 'The value must be a boolean';
}

class BooleanValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if (!is_bool($value))
    {
      $this->setMessage($constraint->message);

      return false;
    }

    return true;
  }
}