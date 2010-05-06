<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Validator\Constraints\Constraint;

interface ConstraintValidatorFactoryInterface
{
  public function getInstance(Constraint $constraint);
}