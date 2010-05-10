<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Validator\LocalExecutionContext;
use Symfony\Components\Validator\Constraint;

interface ConstraintValidatorInterface
{
  public function isValid($value, Constraint $constraint);

  public function getMessageTemplate();

  public function getMessageParameters();
}