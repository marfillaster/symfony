<?php

namespace Symfony\Components\Validator\Engine;

// immutable
class PropertyPathBuilder
{
  private $propertyPath = '';
  private $elements = array();
  private $iterable = array();

  public function atProperty($property)
  {
    $builder = clone $this;

    $builder->elements[] = $property;
    $builder->iterable[] = false;

    if (!empty($builder->propertyPath))
    {
      $property = '.'.$property;
    }

    $builder->propertyPath .= $property;

    return $builder;
  }

  public function atIndex($index)
  {
    $builder = clone $this;

    $builder->propertyPath .= '[' . $index . ']';
    $builder->elements[] = $index;
    $builder->iterable[count($builder->iterable) - 1] = true;
    $builder->iterable[] = false;

    return $builder;
  }

  public function getPropertyPath()
  {
    return new PropertyPath($this->propertyPath, $this->elements, $this->iterable);
  }
}