<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Constraint;
use Symfony\Components\Validator\ConstraintValidator;

class DateTimeValidator extends ConstraintValidator
{
  const PATTERN = '/^(\d{4})-((02-(0[1-9]|[12][0-9]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/';

  public function isValid($value, Constraint $constraint)
  {
    if (!preg_match(self::PATTERN, $value))
    {
      $this->setMessage($constraint->message, array('value' => $value));

      return false;
    }

    return true;
  }
}