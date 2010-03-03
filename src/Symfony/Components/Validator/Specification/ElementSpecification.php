<?php

namespace Symfony\Components\Validator\Specification;

// IMPORTANT: Immutable class!
class ElementSpecification
{
  private $class;
  private $constraints = array();

  public function __construct($class, array $constraints = array())
  {
    $this->class = $class;
    $this->constraints = $constraints;
  }

  public function getClassName()
  {
    return $this->class;
  }

  public function getConstraints()
  {
    return $this->constraints;
  }

  public function hasConstraints()
  {
    return count($this->constraints) > 0;
  }
}