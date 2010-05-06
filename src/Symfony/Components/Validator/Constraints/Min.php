<?php

namespace Symfony\Components\Validator\Constraints;

class Min extends Constraint
{
  public $value;
  public $message;

  public function defaultAttribute()
  {
    return 'value';
  }
}