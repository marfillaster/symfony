<?php

namespace Symfony\Components\Validator\Engine\Execution;

use Symfony\Components\Validator\Engine\ConstraintViolationList;
use Symfony\Components\Validator\Engine\PropertyPathBuilder;

class ValidateObject implements CommandInterface
{
  protected $object;
  protected $propertyPathBuilder;

  public function __construct($object, PropertyPathBuilder $propertyPathBuilder)
  {
    $this->object = $object;
    $this->propertyPathBuilder = $propertyPathBuilder;
  }

  public function getHash()
  {
  }

  public function execute(ConstraintViolationList $violations, ExecutionContext $context)
  {
    $classMetaData = $context->getMetaData()->getClassMetaData(get_class($this->object));

    foreach ($classMetaData->getConstrainedProperties() as $property)
    {
      $context->execute(new ValidateProperty(
        $this->object,
        $property,
        $this->propertyPathBuilder
      ));
    }
  }
}