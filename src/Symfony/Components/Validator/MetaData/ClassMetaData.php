<?php

namespace Symfony\Components\Validator\MetaData;

use Symfony\Components\Validator\Specification\ClassSpecification;

// IMMUTABLE object
class ClassMetaData extends ElementMetaData
{
  private $properties = array();

  public function __construct($className, ClassSpecification $classSpecification = null, ClassMetaData $parent = null, array $interfaces = array())
  {
    parent::__construct($className, $classSpecification, $parent);

    if (!is_null($classSpecification))
    {
      foreach ($classSpecification->getPropertySpecifications() as $propertySpecification)
      {
        $name = $propertySpecification->getPropertyName();
        $parentProperty = isset($parent->properties[$name]) ? $parent->properties[$name] : null;
        $this->properties[$name] = new PropertyMetaData($className, $name, $propertySpecification, $parentProperty);
      }
    }

    if (!is_null($parent))
    {
      $this->mergeProperties($parent);
    }

    foreach ($interfaces as $interface)
    {
      $this->mergeProperties($interface);
      $this->mergeConstraints($interface);
    }
  }

  public function getPropertyMetaData($name)
  {
    // TODO error treatment

    return $this->properties[$name];
  }

  public function getConstrainedProperties()
  {
    return array_keys($this->properties);
  }

  protected function mergeProperties(ClassMetaData $class)
  {
    foreach ($class->properties as $name => $property)
    {
      if (!isset($this->properties[$name]))
      {
        $this->properties[$name] = $property;
      }
    }
  }
}