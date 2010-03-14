<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Engine\Constraint;

class Valid extends Constraint
{
  public $class;
  public $classMessage = 'Value must be instance of %class%';
}