<?php

namespace Symfony\Components\Validator\Tests\MetaData;

require_once __DIR__.'/../TestInit.php';


use Symfony\Components\Validator\MetaData\ElementMetaData;
use Symfony\Components\Validator\Engine\Constraint;
use Symfony\Components\Validator\Specification\ElementSpecification;


interface ParentGroup {}
interface ChildGroup {}

class ElementMetaDataTest_Constraint extends Constraint {}
class ElementMetaDataTest_ParentConstraint extends Constraint {}
class ElementMetaDataTest_ChildConstraint extends Constraint {}


class ElementMetaDataTest extends \PHPUnit_Framework_TestCase
{
  public function testConstraintsAreTakenFromSpecification()
  {
    $constraint = new ElementMetaDataTest_Constraint();
    $spec = new ElementSpecification('Class', array($constraint));
    $element = new ElementMetaData('Class', $spec);

    $expected = array(
      get_class($constraint) => $constraint
    );

    $this->assertEquals($expected, $element->getConstraints());
  }

  public function testParentConstraintsAreMerged()
  {
    $parentConstraint = new ElementMetaDataTest_ParentConstraint();
    $spec = new ElementSpecification('Class', array($parentConstraint));
    $parent = new ElementMetaData('Class', $spec);

    $childConstraint = new ElementMetaDataTest_ChildConstraint();
    $spec = new ElementSpecification('SubClass', array($childConstraint));
    $element = new ElementMetaData('SubClass', $spec, $parent);

    $expected = array(
      get_class($childConstraint) => $childConstraint,
      get_class($parentConstraint) => $parentConstraint,
    );

    $this->assertEquals($expected, $element->getConstraints());
  }

  public function testExistingConstraintsRemainUnchanged()
  {
    $parentConstraint = new ElementMetaDataTest_Constraint();
    $parentConstraint->groups = __NAMESPACE__.'\\ParentGroup';
    $spec = new ElementSpecification('Class', array($parentConstraint));
    $parent = new ElementMetaData('Class', $spec);

    $childConstraint = new ElementMetaDataTest_Constraint();
    $childConstraint->groups = __NAMESPACE__.'\\ChildGroup';
    $spec = new ElementSpecification('SubClass', array($childConstraint));
    $element = new ElementMetaData('SubClass', $spec, $parent);

    $expected = array(
      get_class($childConstraint) => $childConstraint,
    );

    $this->assertEquals($expected, $element->getConstraints());
  }
}