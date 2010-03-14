<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Specification\PropertySpecification;

// IMMUTABLE
class PropertyMetadata extends ElementMetadata
{
  private $propertyName;

  public function __construct($propertyName)
  {
    $this->propertyName = $propertyName;
  }

  public function getPropertyName()
  {
    return $this->propertyName;
  }
}