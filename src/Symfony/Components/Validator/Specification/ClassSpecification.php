<?php

namespace Symfony\Components\Validator\Specification;

// IMPORTANT: Immutable class
class ClassSpecification extends ElementSpecification
{
  private $propertySpecifications;

  public function __construct($class, array $propertySpecifications = array(), array $constraints = array())
  {
    parent::__construct($class, $constraints);

    $this->propertySpecifications = $propertySpecifications;
  }

  public function getPropertySpecifications()
  {
    return $this->propertySpecifications;
  }
}