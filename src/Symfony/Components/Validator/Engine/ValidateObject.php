<?php

namespace Symfony\Components\Validator\Engine;

class ValidateObject implements CommandInterface
{
  protected $object;
  protected $propertyPathBuilder;

  public function __construct($object, PropertyPathBuilder $propertyPathBuilder)
  {
    $this->object = $object;
    $this->propertyPathBuilder = $propertyPathBuilder;
  }

  public function getCacheKey(LocalExecutionContext $context)
  {
    return is_null($this->object) ? null : (implode(',', $context->getGroups()) . ':' . spl_object_hash($this->object));
  }

  public function execute(ConstraintViolationList $violations, LocalExecutionContext $context)
  {
    if (!is_null($this->object))
    {
      $classMetaData = $context->getMetaData()->getClassMetaData(get_class($this->object));

      foreach ($classMetaData->getConstrainedProperties() as $property)
      {
        $context->execute(new ValidateProperty(
          $this->object,
          $property,
          $this->propertyPathBuilder->atProperty($property)
        ));
      }
    }
  }
}