<?php

namespace Symfony\Components\Validator;

interface ConstraintValidatorFactoryInterface
{
  public function getValidator($constraint);
}