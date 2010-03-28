<?php

namespace Symfony\Tests\Components\Validator\Mapping;

require_once __DIR__.'/../../../bootstrap.php';

use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Mapping\ClassMetadataFactory;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\GroupMetadata;
use Symfony\Components\Validator\Mapping\Loader\LoaderInterface;

class FactoryConstraintLoader implements LoaderInterface
{
  public function loadClassMetadata(ClassMetadata $metadata)
  {
    $constraint = new FactoryConstraintA();
    $constraint->definedOn = $metadata->getName();

    $metadata->addConstraint($constraint);

    if ($metadata->getName() == __NAMESPACE__.'\FactoryParent')
    {
      $metadata->addConstraint(new FactoryConstraintB());
    }

    if ($metadata->getName() == __NAMESPACE__.'\FactoryInterface')
    {
      $metadata->addConstraint(new FactoryConstraintC());
    }
  }
}

class FactoryPropertyConstraintLoader implements LoaderInterface
{
  public function loadClassMetadata(ClassMetadata $metadata)
  {
    $constraint = new FactoryConstraintA();
    $constraint->definedOn = $metadata->getName();

    $metadata->addPropertyConstraint('property', $constraint);

    if ($metadata->getName() == __NAMESPACE__.'\FactoryParent')
    {
      $metadata->addPropertyConstraint('property', new FactoryConstraintB());
    }

    if ($metadata->getName() == __NAMESPACE__.'\FactoryInterface')
    {
      $metadata->addPropertyConstraint('property', new FactoryConstraintC());
    }
  }
}

class FactoryGroupSequenceLoader implements LoaderInterface
{
  public function loadClassMetadata(ClassMetadata $metadata)
  {
    $metadata->setGroupSequence(array(
      __NAMESPACE__.'\FactoryGroup1',
      __NAMESPACE__.'\FactoryGroup2',
    ));
  }
}

class ClassMetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
  public function testLoadClassMetadata()
  {
    $factory = new ClassMetadataFactory(new FactoryConstraintLoader());
    $metadata = $factory->getClassMetadata(__NAMESPACE__.'\FactoryParent');

    $constraint1 = new FactoryConstraintA();
    $constraint1->definedOn = __NAMESPACE__.'\FactoryParent';
    $constraint2 = new FactoryConstraintB();

    $this->assertEquals(array($constraint1, $constraint2), array_values($metadata->getConstraints()));
  }

  public function testConstraintsAreIncluded()
  {
    $factory = new ClassMetadataFactory(new FactoryConstraintLoader());
    $metadata = $factory->getClassMetadata(__NAMESPACE__.'\FactoryChild');

    $constraint1 = new FactoryConstraintA();
    $constraint1->definedOn = __NAMESPACE__.'\FactoryChild';
    $constraint2 = new FactoryConstraintB();
    $constraint3 = new FactoryConstraintC();

    $this->assertEquals(array($constraint1, $constraint2, $constraint3), array_values($metadata->getConstraints()));
  }

  public function testParentPropertyConstraintsAreIncluded()
  {
    $factory = new ClassMetadataFactory(new FactoryPropertyConstraintLoader());
    $metadata = $factory->getClassMetadata(__NAMESPACE__.'\FactoryChild');
    $metadata = $metadata->getPropertyMetadata('property');

    $constraint1 = new FactoryConstraintA();
    $constraint1->definedOn = __NAMESPACE__.'\FactoryChild';
    $constraint2 = new FactoryConstraintB();
    $constraint3 = new FactoryConstraintC();

    $this->assertEquals(array($constraint1, $constraint2, $constraint3), array_values($metadata->getConstraints()));
  }

  public function testLoadClassWithSequence()
  {
    $factory = new ClassMetadataFactory(new FactoryGroupSequenceLoader());
    $metadata = $factory->getClassMetadata(__NAMESPACE__.'\FactoryParent');

    $sequence = array(
      __NAMESPACE__.'\FactoryGroup1',
      __NAMESPACE__.'\FactoryGroup2',
    );

    $this->assertEquals($sequence, $metadata->getGroupSequence());
  }
}

class FactoryConstraintA extends Constraint
{
  public $definedOn;
}

class FactoryConstraintB extends Constraint {}
class FactoryConstraintC extends Constraint {}

interface FactoryInterface {}
class FactoryParent {}
class FactoryChild extends FactoryParent implements FactoryInterface {}

class FactoryGroup1 {}
class FactoryGroup2 {}