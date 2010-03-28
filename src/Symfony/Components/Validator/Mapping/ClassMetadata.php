<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Exception\ValidatorException;

class ClassMetadata extends ElementMetadata
{
  protected $name;
  protected $properties = array();
  protected $groupSequence = array();
  protected $reflClass;
  protected $reflProperties;

  public function __construct($name)
  {
    $this->name = $name;
    $this->reflClass = new \ReflectionClass($name);
  }

  public function getClassName()
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

  public function addConstraint(Constraint $constraint)
  {
    $constraint->groups = (array)$constraint->groups;

    $this->addImplicitGroupNames($constraint);

    parent::addConstraint($constraint);

    return $this;
  }

  public function addPropertyConstraint($name, Constraint $constraint)
  {
    if (!isset($this->properties[$name]))
    {
      $this->properties[$name] = new PropertyMetadata($name);
    }

    $constraint->groups = (array)$constraint->groups;

    $this->addImplicitGroupNames($constraint);

    $this->properties[$name]->addConstraint($constraint);

    return $this;
  }

  public function addGetterConstraint($property, Constraint $constraint)
  {
  }

  protected function addImplicitGroupNames(Constraint $constraint)
  {
    if (in_array(Constraint::DEFAULT_GROUP, $constraint->groups) && !in_array($this->name, $constraint->groups))
    {
      $constraint->groups[] = $this->reflClass->getShortName();
    }

    return $this;
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

  public function setGroupSequence(array $groups)
  {
    $this->groupSequence = $groups;

    return $this;
  }

  public function hasGroupSequence()
  {
    return count($this->groupSequence) > 0;
  }

  public function getGroupSequence()
  {
    return $this->groupSequence;
  }
}