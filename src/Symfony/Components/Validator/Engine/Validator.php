<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\SpecificationInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Exception\ValidatorException;
use Symfony\Components\Validator\Exception\UnknownClassException;

class Validator implements ValidatorInterface
{
  protected $metaDataCache;
  protected $validatorFactory;
  protected $classSpecifications = array();

  public function __construct(SpecificationInterface $metaDataCache, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->metaDataCache = new ClassMetaDataCache($metaDataCache);
    $this->validatorFactory = new CachingConstraintValidatorFactory($validatorFactory);
  }

  public function validate($object, $groups = 'default')
  {
    $violations = new ConstraintViolationList();
    $classMetaData = $this->metaDataCache->getClassMetaData(get_class($object));

    if (is_null($classMetaData))
    {
      throw new UnknownClassException(sprintf('No class specification exists for class %s', $class));
    }

    foreach ($classMetaData->getConstrainedProperties() as $property)
    {
      $violations->addAll($this->validateProperty($object, $property, $groups));
    }

    return $violations;
  }

  public function validateProperty($object, $property, $groups = 'default')
  {
    if (isset($object->$property))
    {
      $value = $object->$property;
    }
    else if (method_exists($object, $property))
    {
      $value = $object->$property();
    }
    else
    {
      throw new ValidatorException(sprintf('Neither property %s nor method %s is readable', $property, $method));
    }

    return $this->validateValue(get_class($object), $property, $value, $groups);
  }

  public function validateValue($class, $property, $value, $groups = 'default')
  {
    $violations = new ConstraintViolationList();

    $classMetaData = $this->metaDataCache->getClassMetaData($class);
    $propertyMetaData = $classMetaData->getPropertyMetaData($property);

    $constraints = $propertyMetaData->findConstraints()
        ->inGroups($groups)
        ->getConstraints();


    foreach ($constraints as $constraint)
    {
      if ($constraint->getName() == 'Valid')
      {
        if (is_array($value) || $value instanceof Traversable)
        {
          foreach ($value as $key => $object)
          {
            $violations->addAll($this->validate($object, $groups));
          }
        }
        else
        {
          $violations->addAll($this->validate($value, $groups));
        }
      }
      else
      {
        $validator = $this->validatorFactory->getValidator($constraint->getName());
        $validator->initialize($constraint->getOptions());

        if (!$validator->validate($value))
        {
          $param = $validator->getMessageParameters();

          $violations->add(new ConstraintViolation(
            str_replace(array_keys($param), $param, $validator->getMessageTemplate()),
            $class,
            $property,
            $value
          ));
        }
      }
    }

    return $violations;
  }
}