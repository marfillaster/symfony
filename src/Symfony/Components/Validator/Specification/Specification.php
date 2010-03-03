<?php

namespace Symfony\Components\Validator\Specification;

use Symfony\Components\Validator\SpecificationInterface;
use Symfony\Components\Validator\Exception\UnknownClassException;

// IMPORTANT: Immutable class!
class Specification implements SpecificationInterface
{
  private $classSpecifications = array();
  private $groupSpecifications = array();

  public function __construct(array $classSpecifications = array(), array $groupSpecifications = array())
  {
    foreach ($classSpecifications as $specification)
    {
      $this->classSpecifications[$specification->getClassName()] = $specification;
    }

    foreach ($groupSpecifications as $specification)
    {
      $this->groupSpecifications[$specification->getInterfaceName()] = $specification;
    }
  }

  public function getClassSpecification($class)
  {
    return isset($this->classSpecifications[$class]) ? $this->classSpecifications[$class] : null;
  }

  public function getGroupSpecification($interface)
  {
    return isset($this->groupSpecifications[$interface]) ? $this->groupSpecifications[$interface] : null;
  }
}