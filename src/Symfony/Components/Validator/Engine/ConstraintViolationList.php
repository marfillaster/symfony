<?php

namespace Symfony\Components\Validator\Engine;

class ConstraintViolationList
{
  protected $violations = array();

  public function add(ConstraintViolation $violation)
  {
    $this->violations[] = $violation;
  }

  public function addAll(ConstraintViolationList $violations)
  {
    foreach ($violations->violations as $violation)
    {
      $this->violations[] = $violation;
    }
  }
}