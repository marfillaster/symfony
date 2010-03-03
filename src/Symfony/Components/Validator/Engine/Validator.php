<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\MetaDataInterface;
use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Exception\ValidatorException;
use Symfony\Components\Validator\Exception\UnknownClassException;

class Validator implements ValidatorInterface
{
  protected $metaData;
  protected $validatorFactory;
  protected $classSpecifications = array();

  public function __construct(MetaDataInterface $metaData, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->metaData = $metaData;
    $this->validatorFactory = new CachingConstraintValidatorFactory($validatorFactory);
  }

  public function validate($object, $groups = 'default')
  {
    return $this->doValidateObject(get_class($object), $object, $groups, get_class($object), new PropertyPathBuilder());
  }

  public function validateProperty($object, $property, $groups = 'default')
  {
    return $this->doValidateProperty($object, $property, $groups, get_class($object), new PropertyPathBuilder());
  }

  public function validateValue($class, $property, $value, $groups = 'default')
  {
    return $this->doValidateValue($class, $property, $value, $groups, $class, new PropertyPathBuilder());
  }

  protected function doValidateObject($class, $object, $groups, $root, PropertyPathBuilder $propertyPathBuilder, $classMessage = '')
  {
    $violations = new ConstraintViolationList();

    if (!$object instanceof $class)
    {
      $violations->add(new ConstraintViolation(
        $classMessage,
        array('%class%' => $class),
        $root,
        $propertyPathBuilder->getPropertyPath(),
        $object
      ));
    }
    else
    {
      $classMetaData = $this->metaData->getClassMetaData($class);

      foreach ($classMetaData->getConstrainedProperties() as $property)
      {
        $violations->addAll($this->doValidateProperty($object, $property, $groups, $root, $propertyPathBuilder));
      }
    }

    return $violations;
  }

  protected function doValidateProperty($object, $property, $groups, $root, PropertyPathBuilder $propertyPathBuilder)
  {
    $getter = 'get'.ucfirst($property);
    $isser = 'is'.ucfirst($property);

    if (property_exists($object, $property))
    {
      $value = $object->$property;
    }
    else if (method_exists($object, $getter))
    {
      $value = $object->$getter();
    }
    else
    {
      throw new ValidatorException(sprintf('Neither property "%s" nor method "%s" is readable', $property, $getter));
    }

    return $this->doValidateValue(get_class($object), $property, $value, $groups, $root, $propertyPathBuilder->atProperty($property));
  }

  protected function doValidateValue($class, $property, $value, $groups, $root, PropertyPathBuilder $propertyPathBuilder)
  {
    $violations = new ConstraintViolationList();

    $classMetaData = $this->metaData->getClassMetaData($class);
    $propertyMetaData = $classMetaData->getPropertyMetaData($property);

    $constraints = $propertyMetaData->findConstraints()
        ->inGroups($groups)
        ->getConstraints();

    foreach ($constraints as $constraint)
    {
      if ($constraint->getName() == 'Valid')
      {
        $classMessage = $constraint->getOption('classMessage', 'Must be instance of %class%');

        if (is_array($value) || $value instanceof \Traversable)
        {
          foreach ($value as $key => $object)
          {
            $violations->addAll($this->doValidateObject(
              $constraint->getOption('class', get_class($object)),
              $object,
              $groups,
              $root,
              $propertyPathBuilder->atIndex($key),
              $classMessage
            ));
          }
        }
        else
        {
          $violations->addAll($this->doValidateObject(
            $constraint->getOption('class', get_class($value)),
            $value,
            $groups,
            $root,
            $propertyPathBuilder,
            $classMessage
          ));
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
            $validator->getMessageTemplate(),
            $validator->getMessageParameters(),
            $root,
            $propertyPathBuilder->getPropertyPath(),
            $value
          ));
        }
      }
    }

    return $violations;
  }
}