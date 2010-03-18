<?php

namespace Symfony\Components\Validator\Engine;

class Constraint
{
  public $groups = 'Base';

  public function __construct(array $values = array())
  {
    foreach ($values as $key => $value)
    {
      $this->$key = $value;
    }
  }

  public function requiredFields()
  {
    return array();
  }

  public function validatedBy()
  {
    return get_class($this) . 'Validator';
  }
}