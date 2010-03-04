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

  public function getCacheKey()
  {
    if (is_object($this->value))
    {
      $value = spl_object_hash($this->value);
    }
    else if (is_resource($this->value) || is_array($this->value))
    {
      $value = serialize($this->value);
    }
    else
    {
      $value = $this->value;
    }

    return $this->class . $this->property . $value;
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