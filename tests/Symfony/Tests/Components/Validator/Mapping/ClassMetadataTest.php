<?php

namespace Symfony\Tests\Components\Validator\Mapping;

require_once __DIR__.'/../../../bootstrap.php';

use Symfony\Components\Validator\Engine\Constraint;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\PropertyMetadata;

class ClassMetadataTest_ConstraintA extends Constraint
{
  public $foo;
}

class ClassMetadataTest_ConstraintB extends Constraint {}

class ClassMetadataTest_Class {}

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
  protected $metadata;

  public function setUp()
  {
    $this->metadata = new ClassMetadata(__NAMESPACE__.'\ClassMetadataTest_Class');
  }

  public function testPropertyConstraintsCanBeAdded()
  {
    $this->metadata->addPropertyConstraint('foo', new ClassMetadataTest_ConstraintA());
    $this->metadata->addPropertyConstraint('bar', new ClassMetadataTest_ConstraintB());

    $expected = new PropertyMetadata('foo');
    $expected->addConstraint(new ClassMetadataTest_ConstraintA());

    $this->assertEquals(array('foo', 'bar'), $this->metadata->getConstrainedProperties());
    $this->assertEquals($expected, $this->metadata->getPropertyMetadata('foo'));
  }

  public function testPropertyConstraintsAreUnique()
  {
    $constraint1 = new ClassMetadataTest_ConstraintA();
    $constraint1->foo = 'A';
    $constraint2 = new ClassMetadataTest_ConstraintA();
    $constraint2->foo = 'B';

    $this->metadata->addPropertyConstraint('foo', $constraint1);
    $this->metadata->addPropertyConstraint('foo', $constraint2);

    $expected = new PropertyMetadata('foo');
    $expected->addConstraint($constraint2);

    $this->assertEquals($expected, $this->metadata->getPropertyMetadata('foo'));
  }

}