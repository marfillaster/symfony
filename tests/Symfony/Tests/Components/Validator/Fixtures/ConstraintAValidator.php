<?php

namespace Symfony\Tests\Components\Validator\Fixtures;

use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Constraints\ConstraintValidator;

class ConstraintAValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if ($value != 'VALID')
    {
      $this->setMessage('message', array('param' => 'value'));
      return false;
    }

    return true;
  }
}