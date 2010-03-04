<?php

namespace Symfony\Components\Validator\Engine;

class ValidateReference implements CommandInterface
{
  protected $constraint;
  protected $object;
  protected $propertyPathBuilder;

  public function __construct($object, Constraint $constraint, PropertyPathBuilder $propertyPathBuilder)
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
      if ($this->constraint->class && !$this->object instanceof $this->constraint->class)
      {
        $violations->add(new ConstraintViolation(
          $this->constraint->classMessage,
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