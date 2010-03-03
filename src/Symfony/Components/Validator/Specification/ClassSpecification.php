<?php

namespace Symfony\Components\Validator\Specification;

// IMPORTANT: Immutable class
class ClassSpecification extends ElementSpecification
{
  private $propertySpecifications;
  private $groupSequence;

  public function __construct($class, array $propertySpecifications = array(), array $constraints = array(), array $groupSequence = array())
  {
    parent::__construct($class, $constraints);

    $this->propertySpecifications = $propertySpecifications;
    $this->groupSequence = $groupSequence;
  }

  public function getPropertySpecifications()
  {
    return $this->propertySpecifications;
  }

  public function getGroupSequence()
  {
    return $this->groupSequence;
  }
}