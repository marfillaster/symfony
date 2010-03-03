<?php

namespace Symfony\Components\Validator\Tests\MetaData;

require_once __DIR__.'/../TestInit.php';


use Symfony\Components\Validator\MetaData\ElementMetaData;
use Symfony\Components\Validator\Specification\ConstraintSpecification;
use Symfony\Components\Validator\Specification\ElementSpecification;


class ElementMetaDataTest extends \PHPUnit_Framework_TestCase
{
  public function testConstraintsAreTakenFromSpecification()
  {
    $constraint = new ConstraintSpecification('Constraint');
    $spec = new ElementSpecification('Class', array($constraint));
    $element = new ElementMetaData('Class', $spec);

    $expected = array(
      'Constraint' => $constraint
    );

    $this->assertEquals($expected, $element->getConstraints());
  }

  public function testParentConstraintsAreMerged()
  {
    $parentConstraint = new ConstraintSpecification('ParentConstraint');
    $spec = new ElementSpecification('Class', array($parentConstraint));
    $parent = new ElementMetaData('Class', $spec);

    $childConstraint = new ConstraintSpecification('ChildConstraint');
    $spec = new ElementSpecification('SubClass', array($childConstraint));
    $element = new ElementMetaData('SubClass', $spec, $parent);

    $expected = array(
      'ChildConstraint' => $childConstraint,
      'ParentConstraint' => $parentConstraint,
    );

    $this->assertEquals($expected, $element->getConstraints());
  }

  public function testExistingConstraintsRemainUnchanged()
  {
    $parentConstraint = new ConstraintSpecification('ExistingConstraint', 'parentgroup');
    $spec = new ElementSpecification('Class', array($parentConstraint));
    $parent = new ElementMetaData('Class', $spec);

    $childConstraint = new ConstraintSpecification('ExistingConstraint', 'childgroup');
    $spec = new ElementSpecification('SubClass', array($childConstraint));
    $element = new ElementMetaData('SubClass', $spec, $parent);

    $expected = array(
      'ExistingConstraint' => $childConstraint,
    );

    $this->assertEquals($expected, $element->getConstraints());
  }
}