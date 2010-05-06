<?php

namespace Symfony\Components\Validator\Constraints;

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