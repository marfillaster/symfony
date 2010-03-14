<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Engine\Constraint;

class All extends Constraint
{
  public $constraints = array();
  public $message = 'The value must be traversable';
}