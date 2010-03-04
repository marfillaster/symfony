<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Validator\Engine\LocalExecutionContext;
use Symfony\Components\Validator\Engine\Constraint;

interface ConstraintValidatorInterface
{
  public function initialize(LocalExecutionContext $context);

  public function isValid($value, Constraint $constraint);

  public function getMessageTemplate();

  public function getMessageParameters();
}