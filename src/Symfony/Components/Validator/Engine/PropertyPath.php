<?php

namespace Symfony\Components\Validator\Engine;

// immutable
class PropertyPath implements \Iterator
{
  private $propertyPath;
  private $elements;
  private $iterable;

  public function __construct($propertyPath, array $elements, array $iterable)
  {
    $this->propertyPath = $propertyPath;
    $this->elements = $elements;
    $this->iterable = $iterable;
  }

  public function __toString()
  {
    return $this->propertyPath;
  }

  public function isIterable()
  {
    return $this->valid() ? $this->iterable[$this->key()] : false;
  }

  public function current()
  {
    return current($this->elements);
  }

  public function next()
  {
    next($this->elements);
  }

  public function key()
  {
    return key($this->elements);
  }

  public function valid()
  {
    return !is_null($this->key());
  }

  public function rewind()
  {
    reset($this->elements);
  }
}