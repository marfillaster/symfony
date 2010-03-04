<?php

namespace Symfony\Components\Validator\Engine\Execution;

use Symfony\Components\Validator\Engine\ConstraintViolationList;
use Symfony\Components\Validator\Engine\PropertyPathBuilder;

class ValidateValue implements CommandInterface
{
  protected $class;
  protected $property;
  protected $value;
  protected $propertyPathBuilder;

  public function __construct($class, $property, $value, PropertyPathBuilder $propertyPathBuilder)
  {
    $this->class = $class;
    $this->property = $property;
    $this->value = $value;
    $this->propertyPathBuilder = $propertyPathBuilder;
  }

  public function getHash()
  {
  }

  public function execute(ConstraintViolationList $violations, ExecutionContext $context)
  {
    $classMetaData = $context->getMetaData()->getClassMetaData($this->class);
    $propertyMetaData = $classMetaData->getPropertyMetaData($this->property);

    $constraints = $propertyMetaData->findConstraints()
        ->inGroups($context->getGroups())
        ->getConstraints();

    foreach ($constraints as $constraint)
    {
      $context->execute(new ValidateConstraint(
        $this->value,
        $constraint,
        $this->propertyPathBuilder
      ));
    }
  }
}