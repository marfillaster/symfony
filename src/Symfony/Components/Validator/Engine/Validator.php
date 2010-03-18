<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\ClassMetadataFactoryInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Mapping\ElementMetadata;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Exception\GroupDefinitionException;

class Validator implements ValidatorInterface
{
  protected $metadata;
  protected $validatorFactory;

  public function __construct(ClassMetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->metadataFactory = $metadataFactory;
    $this->validatorFactory = new CachingConstraintValidatorFactory($validatorFactory);
  }

  public function validate($object, $groups = 'Base')
  {
    $classMeta = $this->metadataFactory->getClassMetadata(get_class($object));
    $groups = $classMeta->resolveGroupNames((array)$groups);
    $groups = $this->metadataFactory->getClassMetadatas($groups);

    return $this->validateGraph('walkClass', $object, $classMeta, $classMeta, $object, $groups);
  }

  public function validateProperty($object, $property, $groups = 'Base')
  {
    $classMeta = $this->metadataFactory->getClassMetadata(get_class($object));
    $propertyMeta = $classMeta->getPropertyMetadata($property);
    $value = $classMeta->getPropertyValue($object, $property);
    $groups = $classMeta->resolveGroupNames((array)$groups);
    $groups = $this->metadataFactory->getClassMetadatas($groups);

    return $this->validateGraph('walkProperty', $object, $classMeta, $propertyMeta, $value, $groups, $property);
  }

  public function validateValue($class, $property, $value, $groups = 'Base')
  {
    $classMeta = $this->metadataFactory->getClassMetadata($class);
    $propertyMeta = $classMeta->getPropertyMetadata($property);
    $groups = $classMeta->resolveGroupNames((array)$groups);
    $groups = $this->metadataFactory->getClassMetadatas($groups);

    return $this->validateGraph('walkProperty', $class, $classMeta, $propertyMeta, $value, $groups, $property);
  }

  public function validateConstraint(Constraint $constraint, $value)
  {
    $walker = new GraphWalker($value, $this->metadataFactory, $this->validatorFactory);

    $walker->walkConstraint($constraint, $value, '');

    return $walker->getViolations();
  }

  protected function validateGraph($walkerMethod, $root, ClassMetadata $classMeta, ElementMetadata $metadata, $value, array $groups, $propertyPath = '')
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

  protected function buildGroupChain(ClassMetadata $class, array $groups)
  {
    $chain = new GroupChain();

    foreach ($groups as $group)
    {
      if (!$group instanceof ClassMetadata)
      {
        throw new \InvalidArgumentException('Groups must be instance of ClassMetadata');
      }

      $expandedSequence = array();
      $processedGroups = array();

      if ($group->isDefaultGroup() && $class->hasGroupSequence())
      {
        $this->expandGroupSequence($class, $expandedSequence, $processedGroups);
        $chain->addGroupSequence($expandedSequence);
      }
      else if ($group->hasGroupSequence() && $group != $class)
      {
        $this->expandGroupSequence($group, $expandedSequence, $processedGroups);
        $chain->addGroupSequence($expandedSequence);
      }
      else
      {
        $chain->addGroup($group);
      }
    }

    return $chain;
  }

  private function expandGroupSequence(ClassMetadata $group, array &$expandedSequence, array &$processedGroups)
  {
    if (isset($processedGroups[$group->getName()]))
    {
      throw new GroupDefinitionException('Circle detected!');
    }

    $processedGroups[$group->getName()] = true;

    foreach ($group->getGroupSequence() as $nestedGroup)
    {
      // The group may contain itself in the group sequence
      if ($nestedGroup->hasGroupSequence())
      {
        $this->expandGroupSequence($nestedGroup, $expandedSequence, $processedGroups);
      }
      else
      {
        $expandedSequence[] = $nestedGroup;
      }
    }
  }
}