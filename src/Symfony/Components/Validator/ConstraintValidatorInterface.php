<?php

namespace Symfony\Components\Validator;

interface ConstraintValidatorInterface
{
  public function initialize(array $options);

  public function validate($value);

  public function getMessageTemplate();

  public function getMessageParameters();
}