<?php

namespace Symfony\Tests\Components\Validator\Mapping;

require_once __DIR__.'/../../../bootstrap.php';

use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\PropertyMetadata;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
  protected $metadata;

  public function setUp()
  {
    $this->metadata = new ClassMetadata(__NAMESPACE__.'\ClassEntity');
  }

  public function testPropertyConstraintsCanBeAdded()
  {
    $this->metadata->addPropertyConstraint('foo', new ClassConstraintA());
    $this->metadata->addPropertyConstraint('bar', new ClassConstraintB());

    $expected = new PropertyMetadata('foo');
    $expected->addConstraint(new ClassConstraintA());

    $this->assertEquals(array('foo', 'bar'), $this->metadata->getConstrainedProperties());
    $this->assertEquals($expected, $this->metadata->getPropertyMetadata('foo'));
  }

  public function testPropertyConstraintsAreUnique()
  {
    $constraint1 = new ClassConstraintA();
    $constraint1->foo = 'A';
    $constraint2 = new ClassConstraintA();
    $constraint2->foo = 'B';

    $this->metadata->addPropertyConstraint('foo', $constraint1);
    $this->metadata->addPropertyConstraint('foo', $constraint2);

    $expected = new PropertyMetadata('foo');
    $expected->addConstraint($constraint2);

    $this->assertEquals($expected, $this->metadata->getPropertyMetadata('foo'));
  }
}

class ClassConstraintA extends Constraint
{
  public $foo;
}
class ClassConstraintB extends Constraint {}
class ClassEntity {}