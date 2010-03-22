<?php

namespace Symfony\Tests\Components\Validator\Mapping;

require_once __DIR__.'/../../../bootstrap.php';

use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Mapping\ClassMetadataFactory;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\GroupMetadata;
use Symfony\Components\Validator\Mapping\Loader\LoaderInterface;

class ClassMetadataFactoryTest_ConstraintA extends Constraint
{
  public $definedOn;
}

class ClassMetadataFactoryTest_ConstraintB extends Constraint {}
class ClassMetadataFactoryTest_ConstraintC extends Constraint {}

interface ClassMetadataFactoryTest_Interface {}
class ClassMetadataFactoryTest_Parent {}
class ClassMetadataFactoryTest_Child extends ClassMetadataFactoryTest_Parent implements ClassMetadataFactoryTest_Interface {}

class ClassMetadataFactoryTest_Group1 {}
class ClassMetadataFactoryTest_Group2 {}

class ClassMetadataFactoryTest_ConstraintLoader implements LoaderInterface
{
  public function loadClassMetadata(ClassMetadata $metadata)
  {
    $constraint = new ClassMetadataFactoryTest_ConstraintA();
    $constraint->definedOn = $metadata->getName();

    $metadata->addConstraint($constraint);

    if ($metadata->getName() == __NAMESPACE__.'\ClassMetadataFactoryTest_Parent')
    {
      $metadata->addConstraint(new ClassMetadataFactoryTest_ConstraintB());
    }

    if ($metadata->getName() == __NAMESPACE__.'\ClassMetadataFactoryTest_Interface')
    {
      $metadata->addConstraint(new ClassMetadataFactoryTest_ConstraintC());
    }
  }
}

class ClassMetadataFactoryTest_PropertyConstraintLoader implements LoaderInterface
{
  public function loadClassMetadata(ClassMetadata $metadata)
  {
    $constraint = new ClassMetadataFactoryTest_ConstraintA();
    $constraint->definedOn = $metadata->getName();

    $metadata->addPropertyConstraint('property', $constraint);

    if ($metadata->getName() == __NAMESPACE__.'\ClassMetadataFactoryTest_Parent')
    {
      $metadata->addPropertyConstraint('property', new ClassMetadataFactoryTest_ConstraintB());
    }

    if ($metadata->getName() == __NAMESPACE__.'\ClassMetadataFactoryTest_Interface')
    {
      $metadata->addPropertyConstraint('property', new ClassMetadataFactoryTest_ConstraintC());
    }
  }
}

class ClassMetadataFactoryTest_GroupSequenceLoader implements LoaderInterface
{
  public function loadClassMetadata(ClassMetadata $metadata)
  {
    $metadata->setGroupSequence(array(
      __NAMESPACE__.'\ClassMetadataFactoryTest_Group1',
      __NAMESPACE__.'\ClassMetadataFactoryTest_Group2',
    ));
  }
}

class ClassMetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
  public function testLoadClassMetadata()
  {
    $factory = new ClassMetadataFactory(new ClassMetadataFactoryTest_ConstraintLoader());
    $metadata = $factory->getClassMetadata(__NAMESPACE__.'\ClassMetadataFactoryTest_Parent');

    $constraint1 = new ClassMetadataFactoryTest_ConstraintA();
    $constraint1->definedOn = __NAMESPACE__.'\ClassMetadataFactoryTest_Parent';
    $constraint2 = new ClassMetadataFactoryTest_ConstraintB();

    $this->assertEquals(array($constraint1, $constraint2), array_values($metadata->getConstraints()));
  }

  public function testConstraintsAreIncluded()
  {
    $factory = new ClassMetadataFactory(new ClassMetadataFactoryTest_ConstraintLoader());
    $metadata = $factory->getClassMetadata(__NAMESPACE__.'\ClassMetadataFactoryTest_Child');

    $constraint1 = new ClassMetadataFactoryTest_ConstraintA();
    $constraint1->definedOn = __NAMESPACE__.'\ClassMetadataFactoryTest_Child';
    $constraint2 = new ClassMetadataFactoryTest_ConstraintB();
    $constraint3 = new ClassMetadataFactoryTest_ConstraintC();

    $this->assertEquals(array($constraint1, $constraint2, $constraint3), array_values($metadata->getConstraints()));
  }

  public function testParentPropertyConstraintsAreIncluded()
  {
    $factory = new ClassMetadataFactory(new ClassMetadataFactoryTest_PropertyConstraintLoader());
    $metadata = $factory->getClassMetadata(__NAMESPACE__.'\ClassMetadataFactoryTest_Child');
    $metadata = $metadata->getPropertyMetadata('property');

    $constraint1 = new ClassMetadataFactoryTest_ConstraintA();
    $constraint1->definedOn = __NAMESPACE__.'\ClassMetadataFactoryTest_Child';
    $constraint2 = new ClassMetadataFactoryTest_ConstraintB();
    $constraint3 = new ClassMetadataFactoryTest_ConstraintC();

    $this->assertEquals(array($constraint1, $constraint2, $constraint3), array_values($metadata->getConstraints()));
  }

  public function testLoadClassWithSequence()
  {
    $factory = new ClassMetadataFactory(new ClassMetadataFactoryTest_GroupSequenceLoader());
    $metadata = $factory->getClassMetadata(__NAMESPACE__.'\ClassMetadataFactoryTest_Parent');

    $sequence = array(
      __NAMESPACE__.'\ClassMetadataFactoryTest_Group1',
      __NAMESPACE__.'\ClassMetadataFactoryTest_Group2',
    );

    $this->assertEquals($sequence, $metadata->getGroupSequence());
  }
}