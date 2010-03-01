<?php

namespace Symfony\Components\Validator\Tests\Engine;

require_once __DIR__.'/../TestInit.php';

use Symfony\Components\Validator\Engine\ConstraintFinder;
use Symfony\Components\Validator\Specification\Builder\SpecificationBuilder;
use Symfony\Components\Validator\Specification\ConstraintSpecification;


class ConstraintFinderTest_Class {}
interface ConstraintFinderTest_Interface {}
class ConstraintFinderTest_ExtendingClass extends ConstraintFinderTest_Class {}
class ConstraintFinderTest_ImplementingClass implements ConstraintFinderTest_Interface {}


class ConstraintFinderTest extends \PHPUnit_Framework_TestCase
{
  protected $builder;

  public function setUp()
  {
    $this->builder = new SpecificationBuilder();

    $this->builder->buildClass(__NAMESPACE__.'\\ConstraintFinderTest_Class')
                  ->addConstraint('ClassConstraint');

    $this->builder->buildClass(__NAMESPACE__.'\\ConstraintFinderTest_Interface')
                  ->addConstraint('InterfaceConstraint');
  }

  protected function assertGetConstraints(ConstraintFinder $finder, array $constraints)
  {
    $this->assertEquals($constraints, array_values($finder->getConstraints()));
  }

  public function testFindConstraintsInClass()
  {
    $finder = ConstraintFinder::inClass(
      __NAMESPACE__.'\\ConstraintFinderTest_Class',
      $this->builder->getSpecification()
    );

    $this->assertGetConstraints($finder, array(new ConstraintSpecification('ClassConstraint')));
  }

  public function testFindConstraintsInSuperClass()
  {
    $finder = ConstraintFinder::inClass(
      __NAMESPACE__.'\\ConstraintFinderTest_ExtendingClass',
      $this->builder->getSpecification()
    );

    $this->assertGetConstraints($finder, array(new ConstraintSpecification('ClassConstraint')));
  }

  public function testFindConstraintsInInterface()
  {
    $finder = ConstraintFinder::inClass(
      __NAMESPACE__.'\\ConstraintFinderTest_ImplementingClass',
      $this->builder->getSpecification()
    );

    $this->assertGetConstraints($finder, array(new ConstraintSpecification('InterfaceConstraint')));
  }

  public function testConstraintsInHierarchyAreMerged()
  {
    $this->builder->buildClass(__NAMESPACE__.'\\ConstraintFinderTest_ExtendingClass')
                  ->addConstraint('LocalConstraint');

    $finder = ConstraintFinder::inClass(
      __NAMESPACE__.'\\ConstraintFinderTest_ExtendingClass',
      $this->builder->getSpecification()
    );

    $this->assertGetConstraints($finder, array(new ConstraintSpecification('ClassConstraint'), new ConstraintSpecification('LocalConstraint')));
  }

  public function testConstraintsAreNotMergedIfScopeLocal()
  {
    $this->builder->buildClass(__NAMESPACE__.'\\ConstraintFinderTest_ExtendingClass')
                  ->addConstraint('LocalConstraint');

    $finder = ConstraintFinder
      ::inClass(
        __NAMESPACE__.'\\ConstraintFinderTest_ExtendingClass',
        $this->builder->getSpecification()
      )
      ->inScope(ConstraintFinder::SCOPE_LOCAL);

    $this->assertGetConstraints($finder, array(new ConstraintSpecification('LocalConstraint')));
  }

  public function testOwnConstraintsOverrideSuperClassConstraints()
  {
    $this->builder->buildClass(__NAMESPACE__.'\\ConstraintFinderTest_ExtendingClass')
                  ->addConstraint('ClassConstraint', null, array('foo' => 'bar'));

    $finder = ConstraintFinder::inClass(
      __NAMESPACE__.'\\ConstraintFinderTest_ExtendingClass',
      $this->builder->getSpecification()
    );

    $this->assertGetConstraints($finder, array(new ConstraintSpecification('ClassConstraint', null, array('foo' => 'bar'))));
  }

  public function testOwnConstraintsOverrideInterfaceConstraints()
  {
    $this->builder->buildClass(__NAMESPACE__.'\\ConstraintFinderTest_ImplementingClass')
                  ->addConstraint('InterfaceConstraint', null, array('foo' => 'bar'));

    $finder = ConstraintFinder::inClass(
      __NAMESPACE__.'\\ConstraintFinderTest_ImplementingClass',
      $this->builder->getSpecification()
    );

    $this->assertGetConstraints($finder, array(new ConstraintSpecification('InterfaceConstraint', null, array('foo' => 'bar'))));
  }

  public function testFindPropertyConstraints()
  {
    $this->builder->buildClass(__NAMESPACE__.'\\ConstraintFinderTest_Class')
                  ->addPropertyConstraint('property', 'PropertyConstraint');

    $finder = ConstraintFinder
      ::inClass(
        __NAMESPACE__.'\\ConstraintFinderTest_Class',
        $this->builder->getSpecification()
      )
      ->inProperty('property');

    $this->assertGetConstraints($finder, array(new ConstraintSpecification('PropertyConstraint')));
  }

  public function testFindGroupConstraints()
  {
    $this->builder->buildClass(__NAMESPACE__.'\\ConstraintFinderTest_Class')
                  ->addConstraint('GroupConstraint', 'mygroup');

    $finder = ConstraintFinder
      ::inClass(
        __NAMESPACE__.'\\ConstraintFinderTest_ExtendingClass',
        $this->builder->getSpecification()
      )
      ->inGroups('mygroup');

    $this->assertGetConstraints($finder, array(new ConstraintSpecification('GroupConstraint', 'mygroup')));
  }
}