<?php

namespace Symfony\Tests\Components\Validator\Mapping;

require_once __DIR__.'/../../../bootstrap.php';
require_once __DIR__.'/../Fixtures/Entity.php';
require_once __DIR__.'/../Fixtures/ConstraintA.php';
require_once __DIR__.'/../Fixtures/ConstraintB.php';

use Symfony\Tests\Components\Validator\Fixtures\Entity;
use Symfony\Tests\Components\Validator\Fixtures\ConstraintA;
use Symfony\Tests\Components\Validator\Fixtures\ConstraintB;
use Symfony\Components\Validator\Mapping\ClassMetadataFactory;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\GroupMetadata;
use Symfony\Components\Validator\Mapping\Loader\LoaderInterface;

class ClassMetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
  public function testLoadClassMetadata()
  {
    $factory = new ClassMetadataFactory(new TestLoader());
    $metadata = $factory->getClassMetadata('Symfony\Tests\Components\Validator\Fixtures\EntityParent');

    $constraints = array(
      new ConstraintA(array('groups' => array('Default', 'EntityParent'))),
    );

    $this->assertEquals($constraints, $metadata->getConstraints());
  }

  public function testIncludeParentConstraints()
  {
    $factory = new ClassMetadataFactory(new TestLoader());
    $metadata = $factory->getClassMetadata('Symfony\Tests\Components\Validator\Fixtures\Entity');

    $constraints = array(
      new ConstraintA(array('groups' => array( // from EntityParent
	    'Default',
        'EntityParent',
        'Entity',
      ))),
      new ConstraintA(array('groups' => array( // from EntityInterface
        'Default',
        'EntityInterface',
        'Entity',
      ))),
      new ConstraintA(array('groups' => array( // from Entity
        'Default',
        'Entity',
      ))),
    );

    $this->assertEquals($constraints, $metadata->getConstraints());
  }

  public function testIncludeParentPropertyConstraints()
  {
    $factory = new ClassMetadataFactory(new TestLoader());
    $class = $factory->getClassMetadata('Symfony\Tests\Components\Validator\Fixtures\Entity');
    $metadata = $class->getPropertyMetadata('firstName');

    $constraints = array(
      new ConstraintA(array('groups' => array( // from EntityParent
	    'Default',
        'EntityParent',
        'Entity',
      ))),
      new ConstraintA(array('groups' => array( // from EntityInterface
        'Default',
        'EntityInterface',
        'Entity',
      ))),
      new ConstraintA(array('groups' => array( // from Entity
        'Default',
        'Entity',
      ))),
    );

    $this->assertEquals($constraints, $metadata->getConstraints());
  }
}

class TestLoader implements LoaderInterface
{
  public function loadClassMetadata(ClassMetadata $metadata)
  {
    $metadata->addConstraint(new ConstraintA());
    $metadata->addPropertyConstraint('firstName', new ConstraintA());
  }
}
