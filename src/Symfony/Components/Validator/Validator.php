<?php

namespace Symfony\Components\Validator;

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
    $groupChain = $this->buildGroupChain($classMeta, $groups);

    $closure = function(GraphWalker $walker, $group) use ($classMeta, $object) {
      return $walker->walkClass($classMeta, $object, $group, '');
    };

    return $this->validateGraph($object, $closure, $groupChain);
  }

  public function validateProperty($object, $property, $groups = null)
  {
    $classMeta = $this->metadataFactory->getClassMetadata(get_class($object));
    $groupChain = $this->buildGroupChain($classMeta, $groups);
    $propertyMeta = $classMeta->getPropertyMetadata($property);

    $closure = function(GraphWalker $walker, $group) use ($classMeta, $object) {
      return $walker->walkProperty($propertyMeta, $object, $group, '');
    };

    return $this->validateGraph($object, $closure, $groupChain);
  }

  public function validatePropertyValue($class, $property, $value, $groups = null)
  {
    $classMeta = $this->metadataFactory->getClassMetadata($class);
    $groupChain = $this->buildGroupChain($classMeta, $groups);
    $propertyMeta = $classMeta->getPropertyMetadata($property);

    $closure = function(GraphWalker $walker, $group) use ($classMeta, $value) {
      return $walker->walkPropertyValue($propertyMeta, $value, $group, '');
    };

    return $this->validateGraph($object, $closure, $groupChain);
  }

  public function validateValue($value, Constraint $constraint)
  {
    $walker = new GraphWalker($value, $this->metadataFactory, $this->validatorFactory);

    $walker->walkConstraint($constraint, $value, '');

    return $walker->getViolations();
  }

  protected function validateGraph($root, $closure, GroupChain $groupChain)
  {
    $walker = new GraphWalker($root, $this->metadataFactory, $this->validatorFactory);

    foreach ($groupChain->getGroups() as $group)
    {
      $closure($walker, $group);
    }

    foreach ($groupChain->getGroupSequences() as $sequence)
    {
      $violationCount = count($walker->getViolations());

      foreach ($sequence as $group)
      {
        $closure($walker, $group);

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