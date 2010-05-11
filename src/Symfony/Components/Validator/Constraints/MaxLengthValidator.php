<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Constraint;
use Symfony\Components\Validator\ConstraintValidator;

class MaxLengthValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    $length = function_exists('mb_strlen') ? mb_strlen($value, $constraint->charset) : strlen($value);

    if ($length > $constraint->limit)
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