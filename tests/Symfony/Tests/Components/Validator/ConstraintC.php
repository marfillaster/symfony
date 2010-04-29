<?php

namespace Symfony\Tests\Components\Validator;

use Symfony\Components\Validator\Constraints\Constraint;

class ConstraintC extends Constraint
{
  public $attribute1;

  public function requiredAttributes()
  {
    return array('attribute1');
  }
}