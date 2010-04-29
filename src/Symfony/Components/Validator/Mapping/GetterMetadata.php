<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Exception\ValidatorException;

class GetterMetadata extends AbstractPropertyMetadata
{
  protected $reflMethod;

  /**
   * {@inheritDoc}
   */
  protected function readPropertyValue($object)
  {
    return $this->getReflectionMethod()->invoke($object);
  }

  /**
   * Returns the ReflectionMethod instance for this method.
   *
   * @return ReflectionMethod
   */
  protected function getReflectionMethod()
  {
    if (!isset($this->reflMethod))
    {
      $getMethod = 'get'.ucfirst($this->name);
      $isMethod = 'is'.ucfirst($this->name);

      if ($this->reflClass->hasMethod($getMethod))
      {
        $method = $getMethod;
      }
      else if ($this->reflClass->hasMethod($isMethod))
      {
        $method = $isMethod;
      }
      else
      {
        throw new ValidatorException(sprintf('Neither method %s nor %s exists in class %s', $getMethod, $isMethod, $this->reflClass->getName()));
      }

      $this->reflMethod = $this->reflClass->getMethod($method);

      // setAccessible() only exists in SVN trunk right now
//      $this->reflMethod->setAccessible(true);
    }

    return $this->reflMethod;
  }
}