<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Constraint;
use Symfony\Components\Validator\ConstraintValidator;

class RegexValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if ($value === null)
    {
      return true;
    }

    if (
      ($constraint->match && !preg_match($constraint->pattern, $value))
      ||
      (!$constraint->match && preg_match($constraint->pattern, $value))
    )
    {
      $this->setMessage($constraint->message, array('value' => $value));

      return false;
    }

    return true;
  }
}