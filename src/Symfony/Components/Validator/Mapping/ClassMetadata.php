<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Engine\Constraint;

class ClassMetadata extends ElementMetadata
{
  protected $name;
  protected $properties = array();
  protected $reflClass;
  protected $reflProperties;

  public function __construct($name)
  {
    $this->name = $name;
    $this->reflClass = new \ReflectionClass($name);
  }

  public function getName()
  {
    return $this->name;
  }

  public function getReflectionClass()
  {
    return $this->reflClass;
  }

  protected function getReflectionProperty($property)
  {
    if (!isset($this->reflProperties[$property]))
    {
      $reflProp = $this->reflClass->getProperty($property);
      $reflProp->setAccessible(true);

      $this->reflProperties[$property] = $reflProp;
    }

    return $this->reflProperties[$property];
  }

  public function addPropertyConstraint($name, Constraint $constraint)
  {
    if (!isset($this->properties[$name]))
    {
      $this->properties[$name] = new PropertyMetadata($name);
    }

    $this->properties[$name]->addConstraint($constraint);
  }

  public function getPropertyMetadata($name)
  {
    // TODO error treatment

    return $this->properties[$name];
  }

  public function getConstrainedProperties()
  {
    return array_keys($this->properties);
  }

  public function getPropertyValue($object, $property)
  {
    $getter = 'get'.ucfirst($property);
    $isser = 'is'.ucfirst($property);

    if (property_exists($object, $property))
    {
      $value = $this->getReflectionProperty($property)->getValue($object);
    }
    else if (method_exists($object, $getter))
    {
      $value = $object->$getter();
    }
    else
    {
      throw new ValidatorException(sprintf('Neither property "%s" nor method "%s" is readable in class "%s"', $property, $getter, get_class($object)));
    }

    return $value;
  }
}