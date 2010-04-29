<?php

namespace Symfony\Tests\Components\Validator;

use Symfony\Components\Validator\Constraints\Constraint;

class ConstraintA extends Constraint
{
  public $property1;
  public $property2;

  public function defaultAttribute()
  {
    return 'property2';
  }
}