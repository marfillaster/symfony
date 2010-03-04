<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Validator\Engine\Constraint;

interface ConstraintValidatorInterface
{
  public function isValid($value, Constraint $constraint);

  public function getMessageTemplate();

  public function getMessageParameters();
}