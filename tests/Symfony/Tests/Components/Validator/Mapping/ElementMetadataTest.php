<?php

namespace Symfony\Tests\Components\Validator\Mapping;

require_once __DIR__.'/../../../bootstrap.php';

use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Mapping\ElementMetadata;

class ElementMetadataTest_ConstraintA extends Constraint
{
  public $foo;
}

class ElementMetadataTest_ConstraintB extends Constraint {}

class ElementMetadataTest extends \PHPUnit_Framework_TestCase
{
  protected $metadata;

  public function setUp()
  {
    $this->metadata = new ElementMetadata();
  }

  public function testConstraintsCanBeAdded()
  {
    $this->metadata->addConstraint($constraint1 = new ElementMetadataTest_ConstraintA());
    $this->metadata->addConstraint($constraint2 = new ElementMetadataTest_ConstraintB());

    $this->assertEquals(array($constraint1, $constraint2), array_values($this->metadata->getConstraints()));
  }

  public function testConstraintsAreUnique()
  {
    $constraint1 = new ElementMetadataTest_ConstraintA();
    $constraint1->foo = 'A';
    $constraint2 = new ElementMetadataTest_ConstraintA();
    $constraint2->foo = 'B';

    $this->metadata->addConstraint($constraint1);
    $this->metadata->addConstraint($constraint2);

    $this->assertEquals(array($constraint2), array_values($this->metadata->getConstraints()));
  }

}