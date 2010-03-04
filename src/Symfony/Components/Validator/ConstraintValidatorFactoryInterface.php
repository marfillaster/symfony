<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Validator\Engine\Constraint;

interface ConstraintValidatorFactoryInterface
{
  public function getInstance($className);
}