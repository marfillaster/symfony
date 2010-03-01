<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;

class CachingConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
  protected $decoratedFactory;
  protected $validators = array();

  public function __construct(ConstraintValidatorFactoryInterface $factory)
  {
    $this->decoratedFactory = $factory;
  }

  public function getValidator($constraint)
  {
    if (!isset($this->validators[$constraint]))
    {
      $this->validators[$constraint] = $this->decoratedFactory->getValidator($constraint);
    }

    return $this->validators[$constraint];
  }
}