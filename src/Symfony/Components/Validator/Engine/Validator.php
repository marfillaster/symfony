<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\MetaDataInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Engine\EngineContext;
use Symfony\Components\Validator\Engine\CommandInterface;
use Symfony\Components\Validator\Engine\ValidateObject;
use Symfony\Components\Validator\Engine\ValidateProperty;
use Symfony\Components\Validator\Engine\ValidateValue;

class Validator implements ValidatorInterface
{
  protected $metaData;
  protected $validatorFactory;

  public function __construct(MetaDataInterface $metaData, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->metaData = $metaData;
    $this->validatorFactory = new CachingConstraintValidatorFactory($validatorFactory);
  }

  public function validate($object, $groups = 'Symfony\Components\Validator\Groups\Base')
  {
    $command = new ValidateObject($object, new PropertyPathBuilder());

    return $this->executeInContext(get_class($object), $groups, $command);
  }

  public function validateProperty($object, $property, $groups = 'Symfony\Components\Validator\Groups\Base')
  {
    $builder = new PropertyPathBuilder();
    $command = new ValidateProperty($object, $property, $builder->atProperty($property));

    return $this->executeInContext(get_class($object), $groups, $command);
  }

  public function validateValue($class, $property, $value, $groups = 'Symfony\Components\Validator\Groups\Base')
  {
    $builder = new PropertyPathBuilder();
    $command = new ValidateValue($class, $property, $value, $builder->atProperty($property));

    return $this->executeInContext($class, $groups, $command);
  }

  protected function executeInContext($root, $groups, CommandInterface $command)
  {
    $context = new ExecutionContext($root, (array)$groups, $this->metaData, $this->validatorFactory);

    return $context->execute($command);
  }
}