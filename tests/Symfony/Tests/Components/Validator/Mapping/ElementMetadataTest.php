<?php

namespace Symfony\Tests\Components\Validator\Mapping;

require_once __DIR__.'/../../../bootstrap.php';

use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Mapping\ElementMetadata;

class ElementMetadataTest extends \PHPUnit_Framework_TestCase
{
  protected $metadata;

  public function setUp()
  {
    $this->metadata = new ElementMetadata();
  }

  public function testConstraintsCanBeAdded()
  {
    $this->metadata->addConstraint($constraint1 = new ElementConstraintA());
    $this->metadata->addConstraint($constraint2 = new ElementConstraintB());

    $this->assertEquals(array($constraint1, $constraint2), array_values($this->metadata->getConstraints()));
  }

  public function testConstraintsAreUnique()
  {
    $constraint1 = new ElementConstraintA();
    $constraint1->foo = 'A';
    $constraint2 = new ElementConstraintA();
    $constraint2->foo = 'B';

    $this->metadata->addConstraint($constraint1);
    $this->metadata->addConstraint($constraint2);

    $this->assertEquals(array($constraint2), array_values($this->metadata->getConstraints()));
  }
}

class ElementConstraintA extends Constraint
{
  public $foo;
}
class ElementConstraintB extends Constraint {}