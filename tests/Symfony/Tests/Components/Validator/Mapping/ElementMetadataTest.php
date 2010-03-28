<?php

namespace Symfony\Tests\Components\Validator\Mapping;

require_once __DIR__.'/../../../bootstrap.php';
require_once __DIR__.'/../ConstraintA.php';
require_once __DIR__.'/../ConstraintB.php';

use Symfony\Tests\Components\Validator\ConstraintA;
use Symfony\Tests\Components\Validator\ConstraintB;
use Symfony\Components\Validator\Mapping\ElementMetadata;

class ElementMetadataTest extends \PHPUnit_Framework_TestCase
{
  protected $metadata;

  public function setUp()
  {
    $this->metadata = new ElementMetadata();
  }

  public function testAddConstraints()
  {
    $this->metadata->addConstraint($constraint1 = new ConstraintA());
    $this->metadata->addConstraint($constraint2 = new ConstraintA());

    $this->assertEquals(array($constraint1, $constraint2), $this->metadata->getConstraints());
  }

  public function testMultipleConstraintsOfTheSameType()
  {
    $constraint1 = new ConstraintA(array('attr' => 'A'));
    $constraint2 = new ConstraintA(array('attr' => 'B'));

    $this->metadata->addConstraint($constraint1);
    $this->metadata->addConstraint($constraint2);

    $this->assertEquals(array($constraint1, $constraint2), $this->metadata->getConstraints());
  }

  public function testFindConstraintsByGroup()
  {
    $constraint1 = new ConstraintA(array('groups' => 'TestGroup'));
    $constraint2 = new ConstraintB();

    $this->metadata->addConstraint($constraint1);
    $this->metadata->addConstraint($constraint2);

    $this->assertEquals(array($constraint1), $this->metadata->findConstraints('TestGroup'));
  }
}
