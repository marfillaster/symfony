<?php

namespace Symfony\Components\Validator;

class ValidationContext
{
  protected $root;
  protected $propertyPath;
  protected $class;
  protected $property;
  protected $violations;

  public function __construct($root)
  {
    $this->root = $root;
    $this->violations = new ConstraintViolationList();
  }

  public function __clone()
  {
    $this->violations = clone $this->violations;
  }

  public function addViolation($message, array $params, $invalidValue)
  {
    $this->violations->add(new ConstraintViolation(
      $message,
      $params,
      $this->root,
      $this->propertyPath,
      $invalidValue
    ));
  }

  public function getViolations()
  {
    return $this->violations;
  }

  public function getRoot()
  {
    return $this->root;
  }

  public function setPropertyPath($propertyPath)
  {
    $this->propertyPath = $propertyPath;
  }

  public function getPropertyPath()
  {
    return $this->propertyPath;
  }

  public function setCurrentClass($class)
  {
    $this->class = $class;
  }

  public function getCurrentClass()
  {
    return $this->class;
  }

  public function setCurrentProperty($property)
  {
    $this->property = $property;
  }

  public function getCurrentProperty()
  {
    return $this->property;
  }
}