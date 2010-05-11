<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Constraint;
use Symfony\Components\Validator\ConstraintValidator;

class DateValidator extends ConstraintValidator
{
  const PATTERN = '/^(\d{4})-((02-(0[1-9]|[12][0-9]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01])))$/';

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