<?php

namespace Symfony\Components\Validator\Mapping;

use \ReflectionClass;
use Symfony\Components\Validator\Constraint;

class ElementMetadata
{
  protected $constraints = array();
  protected $constraintsByGroup = array();

  public function addConstraint(Constraint $constraint)
  {
    $this->constraints[] = $constraint;

    foreach ((array)$constraint->groups as $group)
    {
      if (!isset($this->constraintsByGroup[$group]))
      {
        $this->constraintsByGroup[$group] = array();
      }

      $this->constraintsByGroup[$group][] = $constraint;
    }
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

  public function findConstraints($group)
  {
    return isset($this->constraintsByGroup[$group])
        ? $this->constraintsByGroup[$group]
        : array();
  }
}