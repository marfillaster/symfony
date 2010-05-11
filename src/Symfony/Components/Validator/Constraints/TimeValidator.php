<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Constraint;
use Symfony\Components\Validator\ConstraintValidator;

class TimeValidator extends ConstraintValidator
{
  const PATTERN = '/(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/';

  public function isValid($value, Constraint $constraint)
  {
    if ($value === null)
    {
      return true;
    }

    if (!preg_match(self::PATTERN, $value))
    {
      $this->setMessage($constraint->message, array('value' => $value));

      return false;
    }

    return true;
  }
}