<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\Specification\ConstraintSpecification;

class ValidateReference implements CommandInterface
{
  protected $constraint;
  protected $object;
  protected $propertyPathBuilder;

  public function __construct($object, ConstraintSpecification $constraint, PropertyPathBuilder $propertyPathBuilder)
  {
    $this->object = $object;
    $this->constraint = $constraint;
    $this->propertyPathBuilder = $propertyPathBuilder;
  }

  public function getCacheKey(LocalExecutionContext $context)
  {
    return null;
//    return is_null($this->object) ? null : (implode(',', $context->getGroups()) . ':' . spl_object_hash($this->object) . $this->constraint->getName());
  }

  public function execute(ConstraintViolationList $violations, LocalExecutionContext $context)
  {
    if (!is_null($this->object))
    {
      $class = $this->constraint->getOption('class');

      if ($class && !$this->object instanceof $class)
      {
        $violations->add(new ConstraintViolation(
          $this->constraint->getOption('classMessage', 'Must be instance of %class%'),
          array('class' => $class),
          $context->getRoot(),
          $this->propertyPathBuilder->getPropertyPath(),
          $this->object
        ));
      }
      else
      {
        $context->execute(new ValidateObject(
          $this->object,
          $this->propertyPathBuilder
        ));
      }
    }
  }
}