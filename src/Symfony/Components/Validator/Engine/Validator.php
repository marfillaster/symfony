<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\ClassMetadataFactoryInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Mapping\ElementMetadata;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Exception\GroupDefinitionException;

class Validator implements ValidatorInterface
{
  protected $metadataFactory;
  protected $validatorFactory;

  public function __construct(ClassMetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->metadataFactory = $metadataFactory;
    $this->validatorFactory = $validatorFactory;
  }

  public function validate($object, $groups = null)
  {
    $classMeta = $this->metadataFactory->getClassMetadata(get_class($object));

    return $this->validateGraph('walkClass', $object, $classMeta, $classMeta, $object, $groups);
  }

  public function validateProperty($object, $property, $groups = null)
  {
    $classMeta = $this->metadataFactory->getClassMetadata(get_class($object));
    $propertyMeta = $classMeta->getPropertyMetadata($property);
    $value = $classMeta->getPropertyValue($object, $property);

    return $this->validateGraph('walkProperty', $object, $classMeta, $propertyMeta, $value, $groups, $property);
  }

  public function validateValue($class, $property, $value, $groups = null)
  {
    $classMeta = $this->metadataFactory->getClassMetadata($class);
    $propertyMeta = $classMeta->getPropertyMetadata($property);

    return $this->validateGraph('walkProperty', $class, $classMeta, $propertyMeta, $value, $groups, $property);
  }

  public function validateConstraint(Constraint $constraint, $value)
  {
    $walker = new GraphWalker($value, $this->metadataFactory, $this->validatorFactory);

    $walker->walkConstraint($constraint, $value, '');

    return $walker->getViolations();
  }

  protected function validateGraph($walkerMethod, $root, ClassMetadata $classMeta, ElementMetadata $metadata, $value, $groups, $propertyPath = '')
  {
    $walker = new GraphWalker($root, $this->metadataFactory, $this->validatorFactory);
    $groupChain = $this->buildGroupChain($classMeta, $groups);

    foreach ($groupChain->getGroups() as $group)
    {
      $walker->$walkerMethod($metadata, $value, $group, $propertyPath);
    }

    foreach ($groupChain->getGroupSequences() as $sequence)
    {
      $violationCount = count($walker->getViolations());

      foreach ($sequence as $group)
      {
        $walker->$walkerMethod($metadata, $value, $group, $propertyPath);

        if (count($walker->getViolations()) > $violationCount)
        {
          break;
        }
      }
    }

    return $walker->getViolations();
  }

  protected function buildGroupChain(ClassMetadata $class, $groups)
  {
    if (is_null($groups))
    {
      $groups = array(Constraint::DEFAULT_GROUP);
    }
    else
    {
      $groups = (array)$groups;
    }

    $chain = new GroupChain();

    foreach ($groups as $group)
    {
      if ($group == Constraint::DEFAULT_GROUP && $class->hasGroupSequence())
      {
        $chain->addGroupSequence($class->getGroupSequence());
      }
      else
      {
        $chain->addGroup($group);
      }
    }

    return $chain;
  }
}