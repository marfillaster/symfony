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

  public function __construct(SpecificationInterface $specification, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->metaDataCache = new ClassMetaDataCache($specification);
    $this->validatorFactory = new CachingConstraintValidatorFactory($validatorFactory);
  }

  public function validate($object, $groups = 'default')
  {
    return $this->doValidateObject(get_class($object), $object, $groups, get_class($object));
  }

  public function validateProperty($object, $property, $groups = 'default')
  {
    return $this->doValidateProperty($object, $property, $groups, get_class($object));
  }

  public function validateValue($class, $property, $value, $groups = 'default')
  {
    return $this->doValidateValue($class, $property, $value, $groups, $class);
  }

  protected function doValidateObject($class, $object, $groups, $root, $propertyPath = '', $classMessage = '')
  {
    $violations = new ConstraintViolationList();

    if (!$object instanceof $class)
    {
      $violations->add(new ConstraintViolation(
        str_replace('%class%', $class, $classMessage),
        $root,
        $propertyPath,
        $object
      ));
    }
    else
    {
      $classMetaData = $this->metaDataCache->getClassMetaData($class);

      foreach ($classMetaData->getConstrainedProperties() as $property)
      {
        $violations->addAll($this->doValidateProperty($object, $property, $groups, $root, $propertyPath));
      }
    }

    return $violations;
  }

  protected function doValidateProperty($object, $property, $groups, $root, $propertyPath = '')
  {
    if (isset($object->$property))
    {
      $value = $object->$property;
    }
    else if ($method = method_exists($object, $property))
    {
      $value = $object->$property();
    }
    else
    {
      throw new ValidatorException(sprintf('Neither property %s nor method %s is readable', $property, $method));
    }

    return $this->doValidateValue(get_class($object), $property, $value, $groups, $root, $propertyPath);
  }

  protected function doValidateValue($class, $property, $value, $groups, $root, $propertyPath = '')
  {
    $violations = new ConstraintViolationList();

    $propertyPath .= (($propertyPath == '') ? '' : '.') . $property;
    $classMetaData = $this->metaDataCache->getClassMetaData($class);
    $propertyMetaData = $classMetaData->getPropertyMetaData($property);

    $constraints = $propertyMetaData->findConstraints()
        ->inGroups($groups)
        ->getConstraints();

    foreach ($constraints as $constraint)
    {
      if ($constraint->getName() == 'Valid')
      {
        $classMessage = $constraint->getOption('classMessage', 'Must be instance of %class%');

        if (is_array($value) || $value instanceof Traversable)
        {
          foreach ($value as $key => $object)
          {
            $violations->addAll($this->doValidateObject(
              $constraint->getOption('class', get_class($object)),
              $object,
              $groups,
              $root,
              $propertyPath.'['.$key.']',
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
            $propertyPath,
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
            str_replace(array_keys($param), $param, $validator->getMessageTemplate()),
            $root,
            $propertyPath,
            $value
          ));
        }
      }
    }

    return $violations;
  }
}