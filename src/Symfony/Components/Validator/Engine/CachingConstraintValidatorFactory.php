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

  public function getValidator(Constraint $constraint)
  {
    $class = get_class($constraint);

    if (!isset($this->validators[$class]))
    {
      $this->validators[$class] = $this->decoratedFactory->getValidator($constraint);
    }

    return $this->validators[$class];
  }
}