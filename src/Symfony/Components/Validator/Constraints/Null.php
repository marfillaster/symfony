<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Engine\Constraint;
use Symfony\Components\Validator\Engine\ConstraintValidator;

class Null extends Constraint
{
  public $message = 'Value should be null';
}

class NullValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if (!is_null($value))
    {
      $this->setMessage($constraint->message);

      return false;
    }

    return true;
  }
}