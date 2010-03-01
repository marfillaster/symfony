<?php

namespace Symfony\Components\Validator\Specification\Builder;

use Symfony\Components\Validator\Specification\Specification;

class SpecificationBuilder
{
  protected $classSpecificationBuilders = array();

  public function buildClass($class)
  {
    $this->classSpecificationBuilders[$class] = new ClassSpecificationBuilder($class, $this);

    return $this->classSpecificationBuilders[$class];
  }

  public function getSpecification()
  {
    $specifications = array();

    foreach ($this->classSpecificationBuilders as $class => $builder)
    {
      $specifications[$class] = $builder->getClassSpecification();
    }

    return new Specification($specifications);
  }
}