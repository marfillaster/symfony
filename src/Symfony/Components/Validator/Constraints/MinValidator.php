<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Constraint;
use Symfony\Components\Validator\ConstraintValidator;

class MinValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if ($value < $constraint->limit)
    {
      $this->setMessage($constraint->message, array(
        'value' => $value,
        'limit' => $constraint->limit,
      ));

      return false;
    }

    return true;
  }
}