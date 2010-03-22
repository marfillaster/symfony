<?php

namespace Symfony\Components\Validator\Constraints;

class Valid extends Constraint
{
  public $class;
  public $classMessage = 'Value must be instance of %class%';
}