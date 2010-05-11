<?php

namespace Symfony\Components\Validator\Constraints;

class Any extends \Symfony\Components\Validator\Constraint
{
  public $constraints = array();
  public $message = 'Symfony.Validator.Any.message';
}