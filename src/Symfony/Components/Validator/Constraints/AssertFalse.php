<?php

namespace Symfony\Components\Validator\Constraints;

class AssertFalse extends Constraint
{
  public $message = 'The value must be false';
}

class AssertFalseValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if ($value)
    {
      $this->setMessage($constraint->message);

      return false;
    }

    return true;
  }
}