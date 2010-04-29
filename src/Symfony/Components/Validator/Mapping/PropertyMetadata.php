<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Exception\ValidatorException;

class PropertyMetadata extends AbstractPropertyMetadata
{
  protected $reflProperty;

  /**
   * {@inheritDoc}
   */
  protected function readPropertyValue($object)
  {
    return $this->getReflectionProperty()->getValue($object);
  }

  /**
   * Returns the ReflectionProperty instance for this property.
   *
   * @return ReflectionProperty
   */
  protected function getReflectionProperty()
  {
    if (!isset($this->reflProperty))
    {
      if (!$this->reflClass->hasProperty($this->name))
      {
        throw new ValidatorException(sprintf('Property %s does not exist in class "%s"', $property, get_class($object)));
      }

      $this->reflProperty = $this->reflClass->getProperty($this->name);
      $this->reflProperty->setAccessible(true);
    }

    return $this->reflProperty;
  }
}