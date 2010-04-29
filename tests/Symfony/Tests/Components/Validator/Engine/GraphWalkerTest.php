<?php

namespace Symfony\Tests\Components\Validator\Engine;

require_once __DIR__.'/../../../bootstrap.php';
require_once __DIR__.'/../Entity.php';
require_once __DIR__.'/../ConstraintA.php';
require_once __DIR__.'/../ConstraintAValidator.php';

use Symfony\Tests\Components\Validator\Entity;
use Symfony\Tests\Components\Validator\ConstraintA;
use Symfony\Components\Validator\Engine\GraphWalker;
use Symfony\Components\Validator\Engine\ConstraintViolation;
use Symfony\Components\Validator\Engine\ConstraintViolationList;
use Symfony\Components\Validator\Engine\ConstraintValidatorFactory;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\PropertyMetadata;
use Symfony\Components\Validator\Constraints\All;
use Symfony\Components\Validator\Constraints\Any;
use Symfony\Components\Validator\Constraints\Valid;

class GraphWalkerTest extends \PHPUnit_Framework_TestCase
{
  protected $factory;

  public function setUp()
  {
    $this->factory = $this->getMock('Symfony\Components\Validator\ClassMetadataFactoryInterface');
    $this->walker = new GraphWalker('Root', $this->factory, new ConstraintValidatorFactory());
  }

  public function testWalkClassValidatesConstraints()
  {
    $metadata = new ClassMetadata('Symfony\Tests\Components\Validator\Entity');
    $metadata->addConstraint(new ConstraintA());

    $this->walker->walkClass($metadata, new Entity(), 'Default', '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkClassValidatesPropertyConstraints()
  {
    $metadata = new ClassMetadata('Symfony\Tests\Components\Validator\Entity');
    $metadata->addPropertyConstraint('firstName', new ConstraintA());

    $this->walker->walkClass($metadata, new Entity(), 'Default', '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkClassValidatesGetterConstraints()
  {
    $metadata = new ClassMetadata('Symfony\Tests\Components\Validator\Entity');
    $metadata->addGetterConstraint('firstName', new ConstraintA());

    $this->walker->walkClass($metadata, new Entity(), 'Default', '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValueValidatesConstraints()
  {
    $metadata = new PropertyMetadata('firstName');
    $metadata->addConstraint(new ConstraintA());

    $this->walker->walkPropertyValue($metadata, 'value', 'Default', '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValueValidatesArrays_AllElements_Fails()
  {
    $constraint = new All();
    $constraint->constraints = array(new ConstraintA());

    $metadata = new PropertyMetadata('firstName');
    $metadata->addConstraint($constraint);

    $this->walker->walkPropertyValue($metadata, array('foo', 'bar', 'VALID'), 'Default', '');

    $this->assertEquals(2, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValueValidatesArrays_AllElements_Succeeds()
  {
    $constraint = new All();
    $constraint->constraints = array(new ConstraintA());

    $metadata = new PropertyMetadata('firstName');
    $metadata->addConstraint($constraint);

    $this->walker->walkPropertyValue($metadata, array('VALID', 'VALID', 'VALID'), 'Default', '');

    $this->assertEquals(0, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValueValidatesArrays_AnyElement_Fails()
  {
    $constraint = new Any();
    $constraint->constraints = array(new ConstraintA());

    $metadata = new PropertyMetadata('firstName');
    $metadata->addConstraint($constraint);

    $this->walker->walkPropertyValue($metadata, array('foo', 'bar'), 'Default', '');

    $this->assertEquals(2, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValueValidatesArrays_AnyElement_Succeeds()
  {
    $constraint = new Any();
    $constraint->constraints = array(new ConstraintA());

    $metadata = new PropertyMetadata('firstName');
    $metadata->addConstraint($constraint);

    $this->walker->walkPropertyValue($metadata, array('foo', 'VALID'), 'Default', '');

    $this->assertEquals(0, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValueValidatesObjects()
  {
    $metadata = new ClassMetadata('Symfony\Tests\Components\Validator\Entity');
    $metadata->addConstraint(new ConstraintA());

    $this->factory->expects($this->once())
        ->method('getClassMetadata')
        ->with($this->equalTo('Symfony\Tests\Components\Validator\Entity'))
        ->will($this->returnValue($metadata));

    $metadata = new PropertyMetadata('firstName');
    $metadata->addConstraint(new Valid());

    $this->walker->walkPropertyValue($metadata, new Entity(), 'Default', '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValueValidatesObjects_ClassCheck_Fails()
  {
    $constraint = new Valid();
    $constraint->class = 'Foobar';

    $metadata = new PropertyMetadata('firstName');
    $metadata->addConstraint($constraint);

    $this->walker->walkPropertyValue($metadata, new Entity(), 'Default', '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValueValidatesObjects_ClassCheck_Succeeds()
  {
    $metadata = new ClassMetadata('Symfony\Tests\Components\Validator\Entity');

    $this->factory->expects($this->once())
        ->method('getClassMetadata')
        ->with($this->equalTo('Symfony\Tests\Components\Validator\Entity'))
        ->will($this->returnValue($metadata));

    $constraint = new Valid();
    $constraint->class = 'Symfony\Tests\Components\Validator\Entity';

    $metadata = new PropertyMetadata('firstName');
    $metadata->addConstraint($constraint);

    $this->walker->walkPropertyValue($metadata, new Entity(), 'Default', '');

    $this->assertEquals(0, count($this->walker->getViolations()));
  }

  public function testWalkConstraintBuildsAViolationIfFailed()
  {
    $constraint = new ConstraintA();

    $this->walker->walkConstraint($constraint, 'foobar', 'firstName.path');

    $violations = new ConstraintViolationList();
    $violations->add(new ConstraintViolation(
      'message',
      array('param' => 'value'),
      'Root',
      'firstName.path',
      'foobar'
    ));

    $this->assertEquals($violations, $this->walker->getViolations());
  }

  public function testWalkConstraintBuildsNoViolationIfSuccessful()
  {
    $constraint = new ConstraintA();

    $this->walker->walkConstraint($constraint, 'VALID', 'firstName.path');

    $this->assertEquals(0, count($this->walker->getViolations()));
  }
}