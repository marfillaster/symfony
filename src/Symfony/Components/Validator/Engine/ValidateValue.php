<?php

namespace Symfony\Components\Validator\Engine;

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
    return $this->propertyPathBuilder->getPropertyPath()->__toString();
  }

  public function execute(ConstraintViolationList $violations, LocalExecutionContext $context)
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