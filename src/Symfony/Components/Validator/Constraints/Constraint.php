<?php

namespace Symfony\Components\Validator\Constraints;

class Constraint
{
  const DEFAULT_GROUP = 'Default';

  public $groups = self::DEFAULT_GROUP;

  public function __construct($value = null)
  {
    if (is_array($value) && count($value) > 0 && is_string(key($value)))
    {
      foreach ($value as $property => $val)
      {
        // TODO throw exception if property does not exist
        $this->$property = $val;
      }
    }
    else if (!is_null($value) && count($value) > 0)
    {
      $property = $this->defaultAttribute();

      if (is_null($property))
      {
        throw new \Exception(sprintf('Please configure a default attribute for constraint %s', get_class($this)));
      }

      $this->$property = $value;
    }
  }

  public function defaultAttribute()
  {
    return null;
  }

  public function requiredFields()
  {
    return array();
  }

  public function validatedBy()
  {
    return get_class($this) . 'Validator';
  }
}