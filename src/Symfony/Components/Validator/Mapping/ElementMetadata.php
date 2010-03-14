<?php

namespace Symfony\Components\Validator\Mapping;

use \ReflectionClass;
use Symfony\Components\Validator\Engine\Constraint;

class ElementMetadata
{
  private $constraints = array();

  public function addConstraint(Constraint $constraint)
  {
    $this->constraints[get_class($constraint)] = $constraint;
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
}