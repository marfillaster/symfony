<?php

namespace Symfony\Components\Validator\Engine;

use \Traversable;
use Symfony\Components\Validator\Constraints\Valid;

class ValidateConstraint implements CommandInterface
{
  protected $value;
  protected $constraint;
  protected $propertyPathBuilder;

  public function __construct($value, Constraint $constraint, PropertyPathBuilder $propertyPathBuilder)
  {
    $this->value = $value;
    $this->constraint = $constraint;
    $this->propertyPathBuilder = $propertyPathBuilder;
  }

  public function getCacheKey(LocalExecutionContext $context)
  {
    return $this->propertyPathBuilder->getPropertyPath()->__toString() . ':' . get_class($this->constraint);
  }

  public function execute(ConstraintViolationList $violations, LocalExecutionContext $context)
  {
    if ($this->constraint instanceof Valid)
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
      $validator = $context->getValidatorFactory()->getValidator($this->constraint);

      if (!$validator->isValid($this->value, $this->constraint))
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