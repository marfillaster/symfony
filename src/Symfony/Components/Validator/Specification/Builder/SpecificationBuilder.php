<?php

namespace Symfony\Components\Validator\Specification\Builder;

use Symfony\Components\Validator\Specification\Specification;

class SpecificationBuilder
{
  protected $classBuilders = array();
  protected $groupBuilders = array();

  public function buildClass($class)
  {
    $this->classBuilders[$class] = new ClassSpecificationBuilder($class);

    return $this->classBuilders[$class];
  }

  public function buildGroup($interface)
  {
    $this->groupBuilders[$interface] = new GroupSpecificationBuilder($interface);

    return $this->groupBuilders[$interface];
  }

  public function getSpecification()
  {
    $classes = array();
    $groups = array();

    foreach ($this->classBuilders as $builder)
    {
      $classes[] = $builder->getClassSpecification();
    }

    foreach ($this->groupBuilders as $builder)
    {
      $groups[] = $builder->getGroupSpecification();
    }

    return new Specification($classes, $groups);
  }
}