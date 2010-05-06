<?php

namespace Symfony\Components\Validator\Constraints;

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