<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Constraint;
use Symfony\Components\Validator\Exception\ValidatorException;

class ClassMetadata extends ElementMetadata
{
  protected $name;
  protected $properties = array();
  protected $propertyMetas = array();
  protected $getterMetas = array();
  protected $groupSequence = array();
  protected $reflClass;

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

  public function addConstraint(Constraint $constraint)
  {
    $constraint->groups = (array)$constraint->groups;

    $this->addImplicitGroupNames($constraint);

    parent::addConstraint($constraint);

    return $this;
  }

  public function addPropertyConstraint($name, Constraint $constraint)
  {
    if (!isset($this->propertyMetas[$name]))
    {
      $this->propertyMetas[$name] = new PropertyMetadata($name);
      $this->propertyMetas[$name]->setReflectionClass($this->reflClass);
    }

    $constraint->groups = (array)$constraint->groups;

    $this->addImplicitGroupNames($constraint);

    $this->propertyMetas[$name]->addConstraint($constraint);
    $this->properties[$name] = true;

    return $this;
  }

  public function addGetterConstraint($property, Constraint $constraint)
  {
    if (!isset($this->getterMetas[$property]))
    {
      $this->getterMetas[$property] = new GetterMetadata($property);
      $this->getterMetas[$property]->setReflectionClass($this->reflClass);
    }

    $constraint->groups = (array)$constraint->groups;

    $this->addImplicitGroupNames($constraint);

    $this->getterMetas[$property]->addConstraint($constraint);
    $this->properties[$property] = true;

    return $this;
  }

  public function getPropertyMetadata($name)
  {
    return isset($this->propertyMetas[$name]) ? $this->propertyMetas[$name] : null;
  }

  public function getGetterMetadata($property)
  {
    return isset($this->getterMetas[$property]) ? $this->getterMetas[$property] : null;
  }

  public function getConstrainedProperties()
  {
    return array_keys($this->properties);
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

  protected function addImplicitGroupNames(Constraint $constraint)
  {
    if (in_array(Constraint::DEFAULT_GROUP, $constraint->groups) && !in_array($this->name, $constraint->groups))
    {
      $constraint->groups[] = $this->reflClass->getShortName();
    }

    return $this;
  }
}