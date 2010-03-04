<?php

namespace Symfony\Components\Validator\Engine;

class ConstraintViolationList implements \IteratorAggregate
{
  protected $violations = array();

  public function __toString()
  {
    $string = '';

    foreach ($this->violations as $violation)
    {
      $param = $violation->getMessageParameters();
      $message = str_replace(array_keys($param), $param, $violation->getMessageTemplate());
      $string .= <<<EOF
{$violation->getRoot()}.{$violation->getPropertyPath()}:
  $message
  (invalid value = {$violation->getInvalidValue()})

EOF;
    }

    return $string;
  }

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

  public function getIterator()
  {
    return new \ArrayIterator($this->violations);
  }
}