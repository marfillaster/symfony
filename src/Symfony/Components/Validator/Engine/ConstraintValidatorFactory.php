<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;

class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
  public function getInstance($className)
  {
    return new $className();
  }
}