<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Engine\Constraint;

class CachingConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
  protected $decoratedFactory;
  protected $validators = array();

  public function __construct(ConstraintValidatorFactoryInterface $factory)
  {
    $this->decoratedFactory = $factory;
  }

  public function getInstance($className)
  {
    if (!isset($this->validators[$className]))
    {
      $this->validators[$className] = $this->decoratedFactory->getInstance($className);
    }

    return $this->validators[$className];
  }
}