<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Exception\ValidatorException;

abstract class AbstractPropertyMetadata extends ElementMetadata
{
  protected $name;
  protected $reflClass;

  public function __construct($name)
  {
    $this->name = $name;
  }

  /**
   * Returns the name of the property
   *
   * @return string  The property name
   */
  public function getPropertyName()
  {
    return $this->name;
  }

  /**
   * Returns the value of this property in the given object
   *
   * @param  object $object  The object
   * @return mixed           The property value
   */
  public function getPropertyValue($object)
  {
    if (!isset($this->reflClass))
    {
      throw new ValidatorException('A ReflectionClass instance must be set on the property metadata');
    }

    if (!$this->reflClass->isInstance($object))
    {
      throw new ValidatorException(sprintf('The given object must be instance of %s', $this->reflClass->getName()));
    }

    return $this->readPropertyValue($object);
  }

  /**
   * Reads the property value from the given object using reflection
   *
   * @param  object $object  The object
   * @return mixed           The property value
   */
  abstract protected function readPropertyValue($object);

  /**
   * Internal method that allows sharing of ReflectionClass instances.
   *
   * Should only be accessed by ClassMetadata.
   *
   * @param ReflectionClass $reflClass
   */
  public function setReflectionClass(\ReflectionClass $reflClass)
  {
    $this->reflClass = $reflClass;
  }
}