<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;

class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
  public function getValidator(Constraint $constraint)
  {
    $class = $constraint->validatedBy();

    return new $class();
  }
}