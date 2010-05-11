<?php

namespace Symfony\Tests\Components\Validator\Fixtures;

use Symfony\Components\Validator\Constraint;

class ConstraintC extends Constraint
{
  public $attribute1;

  public function requiredAttributes()
  {
    return array('attribute1');
  }
}