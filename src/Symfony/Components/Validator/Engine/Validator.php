<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\ClassMetadataFactoryInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Mapping\ElementMetadata;
use Symfony\Components\Validator\Mapping\ClassMetadata;

class Validator implements ValidatorInterface
{
  protected $metadata;
  protected $validatorFactory;

  public function __construct(ClassMetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->metadataFactory = $metadataFactory;
    $this->validatorFactory = new CachingConstraintValidatorFactory($validatorFactory);
  }

  public function validate($object, $groups = 'Symfony\Components\Validator\Groups\Base')
  {
    $classMeta = $this->metadataFactory->getClassMetadata(get_class($object));

    return $this->validateGraph($object, $classMeta, $object, $groups);
  }

  public function validateProperty($object, $property, $groups = 'Symfony\Components\Validator\Groups\Base')
  {
    $classMeta = $this->metadataFactory->getClassMetadata(get_class($object));
    $propertyMeta = $classMeta->getPropertyMetadata($property);
    $value = $classMeta->getPropertyValue($object, $property);

    return $this->validateGraph($object, $propertyMeta, $value, $groups, $property);
  }

  public function validateValue($class, $property, $value, $groups = 'Symfony\Components\Validator\Groups\Base')
  {
    $classMeta = $this->metadataFactory->getClassMetadata($class);
    $propertyMeta = $classMeta->getPropertyMetadata($property);

    return $this->validateGraph($class, $propertyMeta, $value, $groups, $property);
  }

  public function validateConstraint(Constraint $constraint, $value)
  {
    $walker = new GraphWalker($value, $this->metadataFactory, $this->validatorFactory);

    $walker->walkConstraint($constraint, $value, '');

    return $walker->getViolations();
  }

  protected function validateGraph($root, ElementMetadata $metadata, $value, $groups, $propertyPath = '')
  {
    $walker = new GraphWalker($root, $this->metadataFactory, $this->validatorFactory);

    // TODO: Group sequences can be traversed here

    if ($metadata instanceof ClassMetadata)
    {
      $walker->walkClass($metadata, $value, (array)$groups, $propertyPath);
    }
    else
    {
      $walker->walkProperty($metadata, $value, (array)$groups, $propertyPath);
    }

    return $walker->getViolations();
  }
}