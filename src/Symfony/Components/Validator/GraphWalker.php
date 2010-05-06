<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Validator\ClassMetadataFactoryInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Constraints\All;
use Symfony\Components\Validator\Constraints\Any;
use Symfony\Components\Validator\Constraints\Valid;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\AbstractPropertyMetadata;

class GraphWalker
{
  protected $violations;
  protected $root;
  protected $validatorFactory;
  protected $metadataFactory;

  public function __construct($root, ClassMetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $factory)
  {
    $this->violations = new ConstraintViolationList();
    $this->root = $root;
    $this->validatorFactory = $factory;
    $this->metadataFactory = $metadataFactory;
  }

  public function getViolations()
  {
    return $this->violations;
  }

  public function walkClass(ClassMetadata $metadata, $object, $group, $propertyPath)
  {
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
        $this->violations->add(new ConstraintViolation(
          $constraint->message,
          array(),
          $this->root,
          $propertyPath,
          $value
        ));
      }
      else
      {
        $backup = clone $this->violations;
        $anyValid = false;

        $n = 0;
        foreach ($value as $key => $element)
        {
          foreach ($constraint->constraints as $constr)
          {
            $this->walkDeepConstraint($constr, $element, $group, $propertyPath.'['.$key.']');
          }

          $m = count($this->violations);
          $anyValid = $anyValid || $m == $n;
          $n = $m;
        }

        if ($constraint instanceof Any && $anyValid)
        {
          $this->violations = $backup;
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
        $this->violations->add(new ConstraintViolation(
          $constraint->classMessage,
          array('class' => $constraint->class),
          $this->root,
          $propertyPath,
          $value
        ));
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

    if (!$validator->isValid($value, $constraint))
    {
      $this->violations->add(new ConstraintViolation(
        $validator->getMessageTemplate(),
        $validator->getMessageParameters(),
        $this->root,
        $propertyPath,
        $value
      ));
    }
  }
}