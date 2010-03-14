<?php

namespace Symfony\Components\Validator\Engine;

use \Traversable;
use Symfony\Components\Validator\ClassMetadataFactoryInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Constraints\All;
use Symfony\Components\Validator\Constraints\Any;
use Symfony\Components\Validator\Constraints\Valid;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\PropertyMetadata;

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

  public function walkClass(ClassMetadata $classMeta, $object, array $groups, $propertyPath)
  {
    foreach ($classMeta->findConstraints($groups) as $constraint)
    {
      $this->walkConstraint($constraint, $object, $propertyPath);
    }

    if (!is_null($object))
    {
      foreach ($classMeta->getConstrainedProperties() as $property)
      {
        $propertyMeta = $classMeta->getPropertyMetadata($property);
        $value = $classMeta->getPropertyValue($object, $property);

        $this->walkProperty($propertyMeta, $value, $groups, empty($propertyPath) ? $property : $propertyPath.'.'.$property);
      }
    }
  }

  public function walkProperty(PropertyMetadata $propertyMeta, $value, array $groups, $propertyPath)
  {
    foreach ($propertyMeta->findConstraints($groups) as $constraint)
    {
      $this->walkDeepConstraint($constraint, $value, $groups, $propertyPath);
    }
  }

  protected function walkArray(Constraint $constraint, $value, array $groups, $propertyPath)
  {
    if (!is_null($value))
    {
      if (!is_array($value) && !$value instanceof Traversable)
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

        foreach ($value as $key => $element)
        {
          $n = 0;
          foreach ($constraint->constraints as $constr)
          {
            $this->walkDeepConstraint($constraint, $value, $groups, $propertyPath.'['.$key.']');
            $m = count($this->violations);
            $anyValid = $anyValid || $m == $n;
            $n = $m;
          }
        }

        if ($constraint instanceof Any && $anyValid)
        {
          $this->violations = $backup;
        }
      }
    }
  }

  protected function walkReference(Valid $constraint, $value, array $groups, $propertyPath)
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
        $classMeta = $this->metadataFactory->getClassMetadata(get_class($value));
        $this->walkClass($classMeta, $value, $groups, $propertyPath);
      }
    }
  }

  protected function walkDeepConstraint(Constraint $constraint, $value, array $groups, $propertyPath)
  {
    if ($constraint instanceof All || $constraint instanceof Any)
    {
      $this->walkArray($constraint, $value, $groups, $propertyPath);
    }
    else if ($constraint instanceof Valid)
    {
      $this->walkReference($constraint, $value, $groups, $propertyPath);
    }
    else
    {
      $this->walkConstraint($constraint, $value, $propertyPath);
    }
  }

  public function walkConstraint(Constraint $constraint, $value, $propertyPath)
  {
    // TODO: exception if constraint is Valid, Any or All

    $validator = $this->validatorFactory->getInstance($constraint->validatedBy());

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