<?php

namespace Symfony\Components\Validator\Specification\Builder;

use Symfony\Components\Validator\Engine\Constraint;
use Symfony\Components\Validator\Specification\ClassSpecification;
use Symfony\Components\Validator\Specification\PropertySpecification;
use Symfony\Components\Validator\Specification\ConstraintSpecification;

class ClassSpecificationBuilder
{
  protected $class;

  protected $classConstraints = array();
  protected $propertyConstraints = array();
  protected $groupSequence = array();

  public function __construct($class)
  {
    $this->class = $class;
  }

  public function setGroupSequence(array $groupSequence)
  {
    $this->groupSequence = $groupSequence;

    return $this;
  }

  protected function getConstraint($className, $groups = null, array $options = array())
  {
    $constraint = new $className;

    if (!$constraint instanceof Constraint)
    {
      throw new ConstraintDefinitionException();
    }

    if (!is_null($groups))
    {
      $constraint->groups = $groups;
    }

    foreach ($options as $key => $value)
    {
      $constraint->$key = $value;
    }

    return $constraint;
  }

  public function addConstraint($className, $groups = null, array $options = array())
  {
    $this->classConstraints[$className] = $this->getConstraint($className, $groups, $options);

    return $this;
  }

  public function addPropertyConstraint($property, $className, $groups = null, array $options = array())
  {
    if (!isset($this->propertyConstraints[$property]))
    {
      $this->propertyConstraints[$property] = array();
    }

    $this->propertyConstraints[$property][$className] = $this->getConstraint($className, $groups, $options);

    return $this;
  }

  public function getClassSpecification()
  {
    $propertySpecifications = array();

    foreach ($this->propertyConstraints as $property => $constraints)
    {
      $propertySpecifications[$property] = new PropertySpecification(
        $this->class,
        $property,
        $constraints
      );
    }

    return new ClassSpecification(
      $this->class,
      $propertySpecifications,
      $this->classConstraints,
      $this->groupSequence
    );
  }
}