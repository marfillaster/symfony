<?php

namespace Symfony\Components\Validator\Engine;

class Constraint
{
  public $groups = 'Symfony\Components\Validator\Groups\Base';

  public function requiredFields()
  {
    return array();
  }

  public function validatedBy()
  {
    return get_class($this) . 'Validator';
  }
}