<?php

namespace Symfony\Components\Validator\Engine\Execution;

use Symfony\Components\Validator\Exception\ValidatorException;
use Symfony\Components\Validator\Engine\ConstraintViolationList;
use Symfony\Components\Validator\Engine\PropertyPathBuilder;

class ValidateProperty implements CommandInterface
{
  protected $object;
  protected $property;
  protected $propertyPathBuilder;

  public function __construct($object, $property, PropertyPathBuilder $propertyPathBuilder)
  {
    $this->object = $object;
    $this->property = $property;
    $this->propertyPathBuilder = $propertyPathBuilder;
  }

  public function getHash()
  {
  }

  public function execute(ConstraintViolationList $violations, ExecutionContext $context)
  {
    $getter = 'get'.ucfirst($this->property);
    $isser = 'is'.ucfirst($this->property);

    if (property_exists($this->object, $this->property))
    {
      $value = $this->object->{$this->property};
    }
    else if (method_exists($this->object, $getter))
    {
      $value = $this->object->$getter();
    }
    else
    {
      throw new ValidatorException(sprintf('Neither property "%s" nor method "%s" is readable', $property, $getter));
    }

    $context->execute(new ValidateValue(
      get_class($this->object),
      $this->property,
      $value,
      $this->propertyPathBuilder->atProperty($this->property)
    ));
  }
}