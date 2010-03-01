<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;

class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
  public function getValidator($name)
  {
    $class = 'Symfony\\Components\\Validator\\Validators\\'.$name.'Validator';

    return new $class();
  }
}