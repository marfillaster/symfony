<?php

namespace Symfony\Components\Validator\Engine;

use \Traversable;
use Symfony\Components\Validator\Specification\ConstraintSpecification;

class ValidateConstraint implements CommandInterface
{
  protected $value;
  protected $constraint;
  protected $propertyPathBuilder;

  public function __construct($value, ConstraintSpecification $constraint, PropertyPathBuilder $propertyPathBuilder)
  {
    $this->value = $value;
    $this->constraint = $constraint;
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

    return $value . spl_object_hash($this->constraint);
  }

  public function execute(ConstraintViolationList $violations, ExecutionContext $context)
  {
    if ($this->constraint->getName() == 'Valid')
    {
      if (is_array($this->value) || $this->value instanceof Traversable)
      {
        foreach ($this->value as $key => $object)
        {
          $context->execute(new ValidateReference(
            $object,
            $this->constraint,
            $this->propertyPathBuilder->atIndex($key)
          ));
        }
      }
      else
      {
        $context->execute(new ValidateReference(
          $this->value,
          $this->constraint,
          $this->propertyPathBuilder
        ));
      }
    }
    else
    {
      $validator = $context->getValidatorFactory()->getValidator($this->constraint->getName());
      $validator->initialize($this->constraint->getOptions());

      if (!$validator->validate($this->value))
      {
        $violations->add(new ConstraintViolation(
          $validator->getMessageTemplate(),
          $validator->getMessageParameters(),
          $context->getRoot(),
          $this->propertyPathBuilder->getPropertyPath(),
          $this->value
        ));
      }
    }
  }
}