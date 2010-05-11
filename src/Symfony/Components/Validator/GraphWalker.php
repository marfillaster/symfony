<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Validator\ClassMetadataFactoryInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Constraint;
use Symfony\Components\Validator\Constraints\All;
use Symfony\Components\Validator\Constraints\Any;
use Symfony\Components\Validator\Constraints\Valid;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\AbstractPropertyMetadata;

class GraphWalker
{
  protected $context;
  protected $validatorFactory;
  protected $metadataFactory;

  public function __construct($root, ClassMetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $factory)
  {
    $this->context = new ValidationContext($root);
    $this->validatorFactory = $factory;
    $this->metadataFactory = $metadataFactory;
  }

  public function getViolations()
  {
    return $this->context->getViolations();
  }

  public function walkClass(ClassMetadata $metadata, $object, $group, $propertyPath)
  {
    $this->context->setCurrentClass($metadata->getClassName());

    foreach ($metadata->findConstraints($group) as $constraint)
    {
      $this->walkConstraint($constraint, $object, $propertyPath);
    }

    if (!is_null($object))
    {
      foreach ($metadata->getConstrainedProperties() as $property)
      {
        $localPropertyPath = empty($propertyPath) ? $property : $propertyPath.'.'.$property;

        if ($propMetadata = $metadata->getPropertyMetadata($property))
        {
          $this->walkProperty($propMetadata, $object, $group, $localPropertyPath);
        }

        if ($getterMetadata = $metadata->getGetterMetadata($property))
        {
          $this->walkProperty($getterMetadata, $object, $group, $localPropertyPath);
        }
      }
    }
  }

  public function walkProperty(AbstractPropertyMetadata $metadata, $object, $group, $propertyPath)
  {
    $value = $metadata->getPropertyValue($object);

    $this->walkPropertyValue($metadata, $value, $group, $propertyPath);
  }

  public function walkPropertyValue(AbstractPropertyMetadata $metadata, $value, $group, $propertyPath)
  {
    $this->context->setCurrentProperty($metadata->getPropertyName());

    foreach ($metadata->findConstraints($group) as $constraint)
    {
      $this->walkDeepConstraint($constraint, $value, $group, $propertyPath);
    }
  }

  protected function walkArray(Constraint $constraint, $value, $group, $propertyPath)
  {
    if (!is_null($value))
    {
      if (!is_array($value) && !$value instanceof \Traversable)
      {
        $this->context->setPropertyPath($propertyPath);
        $this->context->addViolation($constraint->message, array(), $value);
      }
      else
      {
        $backup = clone $this->context;
        $anyValid = false;

        $n = 0;
        foreach ($value as $key => $element)
        {
          foreach ($constraint->constraints as $constr)
          {
            $this->walkDeepConstraint($constr, $element, $group, $propertyPath.'['.$key.']');
          }

          $m = count($this->context->getViolations());
          $anyValid = $anyValid || $m == $n;
          $n = $m;
        }

        if ($constraint instanceof Any && $anyValid)
        {
          $this->context = $backup;
        }
      }
    }
  }

  protected function walkReference(Valid $constraint, $value, $group, $propertyPath)
  {
    if (!is_null($value))
    {
      if ($constraint->class && !$value instanceof $constraint->class)
      {
        $this->context->setPropertyPath($propertyPath);
        $this->context->addViolation($constraint->message, array('class' => $constraint->class), $value);
      }
      else
      {
        $metadata = $this->metadataFactory->getClassMetadata(get_class($value));
        $this->walkClass($metadata, $value, $group, $propertyPath);
      }
    }
  }

  protected function walkDeepConstraint(Constraint $constraint, $value, $group, $propertyPath)
  {
    if ($constraint instanceof All || $constraint instanceof Any)
    {
      $this->walkArray($constraint, $value, $group, $propertyPath);
    }
    else if ($constraint instanceof Valid)
    {
      $this->walkReference($constraint, $value, $group, $propertyPath);
    }
    else
    {
      $this->walkConstraint($constraint, $value, $propertyPath);
    }
  }

  public function walkConstraint(Constraint $constraint, $value, $propertyPath)
  {
    // TODO: exception if constraint is Valid

    $validator = $this->validatorFactory->getInstance($constraint);

    $this->context->setPropertyPath($propertyPath);
    $validator->initialize($this->context);

    if (!$validator->isValid($value, $constraint))
    {
      $this->context->addViolation(
        $validator->getMessageTemplate(),
        $validator->getMessageParameters(),
        $value
      );
    }
  }
}