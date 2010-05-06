<?php

namespace Symfony\Components\Validator\Constraints;

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