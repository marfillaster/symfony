<?php

namespace Symfony\Components\Validator\Constraints;

class Any extends Constraint
{
  public $constraints = array();
  public $message = 'The value must be traversable';
}