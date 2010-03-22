<?php

namespace Symfony\Components\Validator\Constraints;

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