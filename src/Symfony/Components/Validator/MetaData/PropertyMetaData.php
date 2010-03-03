<?php

namespace Symfony\Components\Validator\MetaData;

use Symfony\Components\Validator\Specification\PropertySpecification;

// IMMUTABLE
class PropertyMetaData extends ElementMetaData
{
  private $propertyName;

  public function __construct($className, $propertyName, PropertySpecification $specification = null, PropertyMetaData $parent = null)
  {
    parent::__construct($className, $specification, $parent);

    $this->propertyName = $propertyName;
  }

  public function getPropertyName()
  {
    return $this->propertyName;
  }
}