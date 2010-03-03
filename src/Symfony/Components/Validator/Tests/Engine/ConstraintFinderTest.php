<?php

namespace Symfony\Components\Validator\Tests\Engine;

require_once __DIR__.'/../TestInit.php';

use Symfony\Components\Validator\Engine\ConstraintFinder;
use Symfony\Components\Validator\Engine\ElementMetaData;
use Symfony\Components\Validator\Specification\ConstraintSpecification;
use Symfony\Components\Validator\Specification\ElementSpecification;


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
    $constraint1 = new ConstraintSpecification('Constraint1', 'mygroup');
    $constraint2 = new ConstraintSpecification('Constraint2');
    $spec = new ElementSpecification('Class', array($constraint1, $constraint2));
    $metaData = new ElementMetaData('Class', $spec);
    $finder = new ConstraintFinder($metaData);

    $expected = array(
      'Constraint1' => $constraint1,
    );

    $this->assertEquals($expected, $finder->inGroups('mygroup')->getConstraints());
  }

  public function testFindConstraintsInMultipleGroups()
  {
    $constraint1 = new ConstraintSpecification('Constraint1', 'group1');
    $constraint2 = new ConstraintSpecification('Constraint2');
    $constraint3 = new ConstraintSpecification('Constraint3', 'group2');
    $spec = new ElementSpecification('Class', array($constraint1, $constraint2, $constraint3));
    $metaData = new ElementMetaData('Class', $spec);
    $finder = new ConstraintFinder($metaData);

    $expected = array(
      'Constraint1' => $constraint1,
      'Constraint3' => $constraint3,
    );

    $this->assertEquals($expected, $finder->inGroups(array('group1', 'group2'))->getConstraints());
  }
}