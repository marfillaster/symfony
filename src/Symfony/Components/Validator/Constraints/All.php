<?php

namespace Symfony\Components\Validator\Constraints;

class All extends Constraint
{
  public $constraints = array();
  public $message = 'The value must be traversable';
}