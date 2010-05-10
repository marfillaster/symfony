<?php

namespace Symfony\Components\Validator\Constraints;

class All extends \Symfony\Components\Validator\Constraint
{
  public $constraints = array();
  public $message = 'Symfony.Validator.All.message';
}