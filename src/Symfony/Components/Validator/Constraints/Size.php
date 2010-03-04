<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Engine\Constraint;
use Symfony\Components\Validator\Engine\ConstraintValidator;

class Size extends Constraint
{
  public $min;
  public $max;
  public $minMessage = 'Value should not be smaller than %min%';
  public $maxMessage = 'Value should not be greater than %max%';
}

class SizeValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if (!is_null($constraint->min))
    {
      if ($value < $constraint->min)
      {
        $this->setMessage($constraint->minMessage, array('%min%' => $constraint->min));

        return false;
      }
    }

    if (!is_null($constraint->max))
    {
      if ($value > $constraint->max)
      {
        $this->setMessage($constraint->maxMessage, array('%max%' => $constraint->max));

        return false;
      }
    }

    return true;
  }
}