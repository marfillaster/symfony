<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Specification\PropertySpecification;

// IMMUTABLE
class PropertyMetadata extends ElementMetadata
{
  private $name;

  public function __construct($name)
  {
    $this->name = $name;
  }

  public function getPropertyName()
  {
    return $this->name;
  }
}