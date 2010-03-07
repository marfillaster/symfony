<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Engine\Constraint;
use Symfony\Components\Validator\Engine\ConstraintValidator;

class AssertTrue extends Constraint
{
  public $message = 'The value must be true';
}

class AssertTrueValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if (!$value)
    {
      $this->setMessage($constraint->message);

      return false;
    }

    return true;
  }
}