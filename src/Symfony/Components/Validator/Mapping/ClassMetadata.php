<?php

namespace Symfony\Components\Validator\Mapping;

use \ReflectionClass;
use Symfony\Components\Validator\Engine\Constraint;

class ClassMetadata extends ElementMetadata
{
  protected $className;
  protected $properties = array();
  protected $reflClass;

  public function __construct($className)
  {
    $this->className = $className;
    $this->reflClass = new ReflectionClass($className);
  }

  public function getClassName()
  {
    return $this->className;
  }

  public function getReflectionClass()
  {
    return $this->reflClass;
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
      $value = $object->$property;
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