<?php

namespace Symfony\Components\Validator\Constraints;

class NotNull extends Constraint
{
  public $message = 'Value should not be null';
}

class NotNullValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if (is_null($value))
    {
      $this->setMessage($constraint->message);

      return false;
    }

    return true;
  }
}