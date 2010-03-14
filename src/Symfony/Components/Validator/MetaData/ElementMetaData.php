<?php

namespace Symfony\Components\Validator\MetaData;

use \ReflectionClass;
use Symfony\Components\Validator\Specification\ElementSpecification;

// IMMUTABLE
class ElementMetaData
{
  private $className;
  private $constraints = array();

  public function __construct($className, ElementSpecification $specification = null, ElementMetaData $parent = null)
  {
    $this->className = $className;

    if (!is_null($specification))
    {
      foreach ($specification->getConstraints() as $constraint)
      {
        $this->constraints[get_class($constraint)] = $constraint;
      }
    }

    if (!is_null($parent))
    {
      $this->mergeConstraints($parent);
    }
  }

  public function getClassName()
  {
    return $this->className;
  }

  public function getConstraints()
  {
    return $this->constraints;
  }

  public function hasConstraint($name)
  {
    return isset($this->constraints[$name]);
  }

  public function hasConstraints()
  {
    return count($this->constraints) > 0;
  }

  public function findConstraints(array $groups)
  {
    $constraints = array();

    foreach ($this->constraints as $constraint)
    {
      foreach ($groups as $group)
      {
        $reflClass = new ReflectionClass($group);
        foreach ((array)$constraint->groups as $constraintGroup)
        {
          if ($reflClass->implementsInterface($constraintGroup))
          {
            $constraints[get_class($constraint)] = $constraint;
            break 2;
          }
        }
      }
    }

    return $constraints;
  }

  protected function mergeConstraints(ElementMetaData $metaData)
  {
    foreach ($metaData->constraints as $name => $constraint)
    {
      if (!isset($this->constraints[$name]))
      {
        $this->constraints[$name] = $constraint;
      }
    }
  }
}