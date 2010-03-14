<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\MetaDataInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\MetaData\ElementMetaData;
use Symfony\Components\Validator\MetaData\ClassMetaData;

class Validator implements ValidatorInterface
{
  protected $metaData;
  protected $validatorFactory;

  public function __construct(MetaDataInterface $metaData, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->metaData = $metaData;
    $this->validatorFactory = new CachingConstraintValidatorFactory($validatorFactory);
  }

  public function validate($object, $groups = 'Symfony\Components\Validator\Groups\Base')
  {
    $classMeta = $this->metaData->getClassMetaData(get_class($object));

    return $this->validateGraph($object, $classMeta, $object, $groups);
  }

  public function validateProperty($object, $property, $groups = 'Symfony\Components\Validator\Groups\Base')
  {
    $classMeta = $this->metaData->getClassMetaData(get_class($object));
    $propertyMeta = $classMeta->getPropertyMetaData($property);
    $value = $classMeta->getPropertyValue($object, $property);

    return $this->validateGraph($object, $propertyMeta, $value, $groups, $property);
  }

  public function validateValue($class, $property, $value, $groups = 'Symfony\Components\Validator\Groups\Base')
  {
    $classMeta = $this->metaData->getClassMetaData($class);
    $propertyMeta = $classMeta->getPropertyMetaData($property);

    return $this->validateGraph($class, $propertyMeta, $value, $groups, $property);
  }

  public function validateConstraint(Constraint $constraint, $value)
  {
    $walker = new GraphWalker($value, $this->metaData, $this->validatorFactory);

    $walker->walkConstraint($constraint, $value, '');

    return $walker->getViolations();
  }

  protected function validateGraph($root, ElementMetaData $metaData, $value, $groups, $propertyPath = '')
  {
    $walker = new GraphWalker($root, $this->metaData, $this->validatorFactory);

    // TODO: Group sequences can be traversed here

    if ($metaData instanceof ClassMetaData)
    {
      $walker->walkClass($metaData, $value, (array)$groups, $propertyPath);
    }
    else
    {
      $walker->walkProperty($metaData, $value, (array)$groups, $propertyPath);
    }

    return $walker->getViolations();
  }
}