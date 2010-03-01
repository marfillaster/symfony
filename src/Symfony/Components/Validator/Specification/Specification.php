<?php

namespace Symfony\Components\Validator\Specification;

use Symfony\Components\Validator\SpecificationInterface;
use Symfony\Components\Validator\Exception\UnknownClassException;

// IMPORTANT: Immutable class!
class Specification implements SpecificationInterface
{
  private $classSpecifications = array();

  public function __construct(array $classSpecifications)
  {
    $this->classSpecifications = $classSpecifications;
  }

  public function getClassSpecification($class)
  {
    return isset($this->classSpecifications[$class]) ? $this->classSpecifications[$class] : null;
  }
}