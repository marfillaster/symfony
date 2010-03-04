<?php

namespace Symfony\Components\Validator\Tests\MetaData;

require_once __DIR__.'/../TestInit.php';

use Symfony\Components\Validator\MetaData\ConstraintFinder;
use Symfony\Components\Validator\MetaData\ElementMetaData;
use Symfony\Components\Validator\MetaData\GroupMetaData;
use Symfony\Components\Validator\Specification\ConstraintSpecification;
use Symfony\Components\Validator\Specification\ElementSpecification;

interface Group {}
interface OtherGroup {}
interface InheritingGroup extends Group {}

class ConstraintFinderTest extends \PHPUnit_Framework_TestCase
{
  public function testFindConstraints()
  {
    $constraint1 = new ConstraintSpecification('Constraint1');
    $constraint2 = new ConstraintSpecification('Constraint2');
    $spec = new ElementSpecification('Class', array($constraint1, $constraint2));
    $metaData = new ElementMetaData('Class', $spec);
    $finder = new ConstraintFinder($metaData);

    $expected = array(
      'Constraint1' => $constraint1,
      'Constraint2' => $constraint2,
    );

    $this->assertEquals($expected, $finder->getConstraints());
  }

  public function testFindConstraintsInOneGroup()
  {
    $constraint1 = new ConstraintSpecification('Constraint1', __NAMESPACE__.'\Group');
    $constraint2 = new ConstraintSpecification('Constraint2');
    $spec = new ElementSpecification('Class', array($constraint1, $constraint2));
    $metaData = new ElementMetaData('Class', $spec);
    $finder = new ConstraintFinder($metaData);

    $group = new GroupMetaData(__NAMESPACE__.'\Group');

    $expected = array(
      'Constraint1' => $constraint1,
    );

    $this->assertEquals($expected, $finder->inGroups($group)->getConstraints());
  }

  public function testFindConstraintsInInheritingGroup()
  {
    $constraint1 = new ConstraintSpecification('Constraint1', __NAMESPACE__.'\Group');
    $constraint2 = new ConstraintSpecification('Constraint2');
    $spec = new ElementSpecification('Class', array($constraint1, $constraint2));
    $metaData = new ElementMetaData('Class', $spec);
    $finder = new ConstraintFinder($metaData);

    $group = new GroupMetaData(__NAMESPACE__.'\InheritingGroup');

    $expected = array(
      'Constraint1' => $constraint1,
    );

    $this->assertEquals($expected, $finder->inGroups($group)->getConstraints());
  }

  public function testFindConstraintsInMultipleGroups()
  {
    $constraint1 = new ConstraintSpecification('Constraint1', __NAMESPACE__.'\Group');
    $constraint2 = new ConstraintSpecification('Constraint2');
    $constraint3 = new ConstraintSpecification('Constraint3', __NAMESPACE__.'\OtherGroup');
    $spec = new ElementSpecification('Class', array($constraint1, $constraint2, $constraint3));
    $metaData = new ElementMetaData('Class', $spec);
    $finder = new ConstraintFinder($metaData);

    $group1 = new GroupMetaData(__NAMESPACE__.'\Group');
    $group2 = new GroupMetaData(__NAMESPACE__.'\OtherGroup');

    $expected = array(
      'Constraint1' => $constraint1,
      'Constraint3' => $constraint3,
    );

    $this->assertEquals($expected, $finder->inGroups(array($group1, $group2))->getConstraints());
  }
}