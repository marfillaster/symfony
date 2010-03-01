<?php

namespace Symfony\Components\Validator\Specification\Builder;

use Symfony\Components\Validator\Specification\ClassSpecification;
use Symfony\Components\Validator\Specification\PropertySpecification;
use Symfony\Components\Validator\Specification\ConstraintSpecification;

class ClassSpecificationBuilder
{
  protected $metaDataBuilder;
  protected $class;

  protected $classConstraints = array();
  protected $propertyConstraints = array();
  protected $parents = array();

  public function __construct($class, SpecificationBuilder $metaDataBuilder)
  {
    $this->class = $class;
    $this->metaDataBuilder = $metaDataBuilder;
  }

  public function addConstraint($name, $groups = null, array $options = array())
  {
    $this->classConstraints[$name] = new ConstraintSpecification($name, $groups, $options);

    return $this;
  }

  public function addPropertyConstraint($property, $name, $groups = null, array $options = array())
  {
    if (!isset($this->propertyConstraints[$property]))
    {
      $this->propertyConstraints[$property] = array();
    }

    $this->propertyConstraints[$property][$name] = new ConstraintSpecification($name, $groups, $options);

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
      $this->classConstraints
    );
  }
}