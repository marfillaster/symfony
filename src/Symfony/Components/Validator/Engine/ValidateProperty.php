<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\Exception\ValidatorException;

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

  public function getCacheKey(LocalExecutionContext $context)
  {
    return null;
//    return is_null($this->object) ? null : (implode(',', $context->getGroups()) . ':' . spl_object_hash($this->object) . $this->property);
  }

  public function execute(ConstraintViolationList $violations, LocalExecutionContext $context)
  {
    if (!is_null($this->object))
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
        throw new ValidatorException(sprintf('Neither property "%s" nor method "%s" is readable in class "%s"', $this->property, $getter, get_class($this->object)));
      }

      $context->execute(new ValidateValue(
        get_class($this->object),
        $this->property,
        $value,
        $this->propertyPathBuilder
      ));
    }
  }
}