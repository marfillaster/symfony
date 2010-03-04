<?php

namespace Symfony\Components\Validator\Engine\Execution;

use Symfony\Components\Validator\Engine\ConstraintViolationList;
use Symfony\Components\Validator\Engine\PropertyPathBuilder;
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

  public function getHash()
  {
  }

  public function execute(ConstraintViolationList $violations, ExecutionContext $context)
  {
    $class = $this->constraint->getOption('class');

    if ($class && !$this->object instanceof $class)
    {
      $violations->add(new ConstraintViolation(
        $this->constraint->getOption('classMessage', 'Must be instance of %class%'),
        array('class' => $class),
        $context->getRoot(),
        $this->propertyPathBuilder->getPropertyPath(),
        $object
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