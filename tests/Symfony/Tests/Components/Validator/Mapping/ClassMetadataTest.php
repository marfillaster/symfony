<?php

namespace Symfony\Tests\Components\Validator\Mapping;

require_once __DIR__.'/../../../bootstrap.php';
require_once __DIR__.'/../Entity.php';
require_once __DIR__.'/../ConstraintA.php';
require_once __DIR__.'/../ConstraintB.php';

use Symfony\Tests\Components\Validator\Entity;
use Symfony\Tests\Components\Validator\ConstraintA;
use Symfony\Tests\Components\Validator\ConstraintB;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\PropertyMetadata;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
  protected $metadata;

  public function setUp()
  {
    $this->metadata = new ClassMetadata('Symfony\Tests\Components\Validator\Entity');
  }

  public function testAddPropertyConstraints()
  {
    $this->metadata->addPropertyConstraint('firstName', new ConstraintA());
    $this->metadata->addPropertyConstraint('bar', new ConstraintB());

    $this->assertEquals(array('firstName', 'bar'), $this->metadata->getConstrainedProperties());
  }

  public function testConstraintsReceiveDefaultGroups()
  {
    $this->metadata->addConstraint(new ConstraintA());

    $constraints = array(new ConstraintA(array('groups' => array(
      'Default',
      'Entity',
    ))));

    $this->assertEquals($constraints, $this->metadata->getConstraints());
  }

  public function testPropertyConstraintsReceiveDefaultGroups()
  {
    $this->metadata->addPropertyConstraint('firstName', new ConstraintA());

    $constraints = array(new ConstraintA(array('groups' => array(
      'Default',
      'Entity',
    ))));

    $this->assertEquals($constraints, $this->metadata->getPropertyMetadata('firstName')->getConstraints());
  }

  public function testMultipleConstraintsOfTheSameType()
  {
    $constraint1 = new ConstraintA(array('property1' => 'A'));
    $constraint2 = new ConstraintA(array('property1' => 'B'));

    $this->metadata->addPropertyConstraint('firstName', $constraint1);
    $this->metadata->addPropertyConstraint('firstName', $constraint2);

    $expected = array($constraint1, $constraint2);

    $this->assertEquals($expected, $this->metadata->getPropertyMetadata('firstName')->getConstraints());
  }
}

