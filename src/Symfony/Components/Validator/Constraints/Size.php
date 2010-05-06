<?php

namespace Symfony\Components\Validator\Constraints;

class Size extends Constraint
{
  public $min;
  public $max;
  public $minMessage = 'Value should not be smaller than %min%';
  public $maxMessage = 'Value should not be greater than %max%';
}
