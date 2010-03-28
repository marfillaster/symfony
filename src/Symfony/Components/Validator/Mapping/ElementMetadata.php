<?php

namespace Symfony\Components\Validator\Mapping;

use \ReflectionClass;
use Symfony\Components\Validator\Constraints\Constraint;

class ElementMetadata
{
  private $constraints = array();
  private $constraintMap = array();

  public function addConstraint(Constraint $constraint)
  {
    $class = get_class($constraint);
    $groups = (array)$constraint->groups;
    $this->constraints[$class] = $constraint;

    $this->addToConstraintMap($groups, $constraint);

    foreach ($groups as $group)
    {
      $this->addToConstraintMap(class_parents($group), $constraint);
      $this->addToConstraintMap(class_implements($group), $constraint);
    }
  }

  private function addToConstraintMap(array $groups, Constraint $constraint)
  {
    $class = get_class($constraint);

    foreach ($groups as $group)
    {
      if (!isset($this->constraintMap[$group]))
      {
        $this->constraintMap[$group] = array();
      }

      $this->constraintMap[$group][$class] = $constraint;
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

  public function findConstraints(ClassMetadata $group)
  {
    $name = $group->getName();

    return isset($this->constraintMap[$name]) ? $this->constraintMap[$name] : array();
  }
}