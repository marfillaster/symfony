<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Constraints\Constraint;

class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
  protected $validators = array();

  public function getInstance(Constraint $constraint)
  {
    $className = $constraint->validatedBy();

    if (!isset($this->validators[$className]))
    {
      $this->validators[$className] = new $className();
    }

    return $this->validators[$className];
  }
}