<?php

namespace Symfony\Components\Validator\Specification;

// IMPORTANT: Immutable class!
class PropertySpecification extends ElementSpecification
{
  private $property;

  public function __construct($class, $property, array $constraints)
  {
    parent::__construct($class, $constraints);

    $this->property = $property;
  }

  public function getPropertyName()
  {
    return $this->property;
  }
}