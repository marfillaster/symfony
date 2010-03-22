<?php

namespace Symfony\Tests\Components\Validator\Engine;

require_once __DIR__.'/../../../bootstrap.php';

use Symfony\Components\Validator\Engine\GraphWalker;
use Symfony\Components\Validator\Constraints\Constraint;
use Symfony\Components\Validator\Engine\ConstraintViolation;
use Symfony\Components\Validator\Engine\ConstraintViolationList;
use Symfony\Components\Validator\Engine\ConstraintValidator;
use Symfony\Components\Validator\Engine\ConstraintValidatorFactory;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\PropertyMetadata;
use Symfony\Components\Validator\Constraints\All;
use Symfony\Components\Validator\Constraints\Any;
use Symfony\Components\Validator\Constraints\Valid;

class GraphWalkerTest_Class
{
  private $property;
}

class GraphWalkerTest_Constraint extends Constraint {}
class GraphWalkerTest_ConstraintValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if ($value != 'CORRECT')
    {
      $this->setMessage('message', array('param' => 'value'));
      return false;
    }

    return true;
  }
}

class GraphWalkerTest extends \PHPUnit_Framework_TestCase
{
  const DEFAULT_GROUP = 'Symfony\Components\Validator\Groups\Base';
  const ROOT = 'Root';

  protected $metadataFactory;
  protected $validatorFactory;

  public function setUp()
  {
    $this->metadataFactory = $this->getMock('Symfony\Components\Validator\ClassMetadataFactoryInterface');
    $this->walker = new GraphWalker(self::ROOT, $this->metadataFactory, new ConstraintValidatorFactory());
  }

  public function testWalkClassValidatesConstraints()
  {
    $metadata = new ClassMetadata(__NAMESPACE__.'\GraphWalkerTest_Class');
    $metadata->addConstraint(new GraphWalkerTest_Constraint());

    $this->walker->walkClass($metadata, new GraphWalkerTest_Class(), array(self::DEFAULT_GROUP), '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkClassValidatesPropertyConstraints()
  {
    $metadata = new ClassMetadata(__NAMESPACE__.'\GraphWalkerTest_Class');
    $metadata->addPropertyConstraint('property', new GraphWalkerTest_Constraint());

    $this->walker->walkClass($metadata, new GraphWalkerTest_Class(), array(self::DEFAULT_GROUP), '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValidatesConstraints()
  {
    $metadata = new PropertyMetadata('property');
    $metadata->addConstraint(new GraphWalkerTest_Constraint());

    $this->walker->walkProperty($metadata, 'value', array(self::DEFAULT_GROUP), '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValidatesArrays_AllElements_Fails()
  {
    $constraint = new All();
    $constraint->constraints = array(new GraphWalkerTest_Constraint());

    $metadata = new PropertyMetadata('property');
    $metadata->addConstraint($constraint);

    $this->walker->walkProperty($metadata, array('foo', 'bar', 'CORRECT'), array(self::DEFAULT_GROUP), '');

    $this->assertEquals(2, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValidatesArrays_AllElements_Succeeds()
  {
    $constraint = new All();
    $constraint->constraints = array(new GraphWalkerTest_Constraint());

    $metadata = new PropertyMetadata('property');
    $metadata->addConstraint($constraint);

    $this->walker->walkProperty($metadata, array('CORRECT', 'CORRECT', 'CORRECT'), array(self::DEFAULT_GROUP), '');

    $this->assertEquals(0, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValidatesArrays_AnyElement_Fails()
  {
    $constraint = new Any();
    $constraint->constraints = array(new GraphWalkerTest_Constraint());

    $metadata = new PropertyMetadata('property');
    $metadata->addConstraint($constraint);

    $this->walker->walkProperty($metadata, array('foo', 'bar'), array(self::DEFAULT_GROUP), '');

    $this->assertEquals(2, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValidatesArrays_AnyElement_Succeeds()
  {
    $constraint = new Any();
    $constraint->constraints = array(new GraphWalkerTest_Constraint());

    $metadata = new PropertyMetadata('property');
    $metadata->addConstraint($constraint);

    $this->walker->walkProperty($metadata, array('foo', 'CORRECT'), array(self::DEFAULT_GROUP), '');

    $this->assertEquals(0, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValidatesObjects()
  {
    $metadata = new ClassMetadata(__NAMESPACE__.'\GraphWalkerTest_Class');
    $metadata->addConstraint(new GraphWalkerTest_Constraint());

    $this->metadataFactory->expects($this->once())
        ->method('getClassMetadata')
        ->with($this->equalTo(__NAMESPACE__.'\GraphWalkerTest_Class'))
        ->will($this->returnValue($metadata));

    $metadata = new PropertyMetadata('property');
    $metadata->addConstraint(new Valid());

    $this->walker->walkProperty($metadata, new GraphWalkerTest_Class(), array(self::DEFAULT_GROUP), '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValidatesObjects_ClassCheck_Fails()
  {
    $constraint = new Valid();
    $constraint->class = 'Foobar';

    $metadata = new PropertyMetadata('property');
    $metadata->addConstraint($constraint);

    $this->walker->walkProperty($metadata, new GraphWalkerTest_Class(), array(self::DEFAULT_GROUP), '');

    $this->assertEquals(1, count($this->walker->getViolations()));
  }

  public function testWalkPropertyValidatesObjects_ClassCheck_Succeeds()
  {
    $metadata = new ClassMetadata(__NAMESPACE__.'\GraphWalkerTest_Class');

    $this->metadataFactory->expects($this->once())
        ->method('getClassMetadata')
        ->with($this->equalTo(__NAMESPACE__.'\GraphWalkerTest_Class'))
        ->will($this->returnValue($metadata));

    $constraint = new Valid();
    $constraint->class = __NAMESPACE__.'\GraphWalkerTest_Class';

    $metadata = new PropertyMetadata('property');
    $metadata->addConstraint($constraint);

    $this->walker->walkProperty($metadata, new GraphWalkerTest_Class(), array(self::DEFAULT_GROUP), '');

    $this->assertEquals(0, count($this->walker->getViolations()));
  }

  public function testWalkConstraintBuildsAViolationIfFailed()
  {
    $constraint = new GraphWalkerTest_Constraint();

    $this->walker->walkConstraint($constraint, 'foobar', 'property.path');

    $violations = new ConstraintViolationList();
    $violations->add(new ConstraintViolation(
      'message',
      array('param' => 'value'),
      self::ROOT,
      'property.path',
      'foobar'
    ));

    $this->assertEquals($violations, $this->walker->getViolations());
  }

  public function testWalkConstraintBuildsNoViolationIfSuccessful()
  {
    $constraint = new GraphWalkerTest_Constraint();

    $this->walker->walkConstraint($constraint, 'CORRECT', 'property.path');

    $this->assertEquals(0, count($this->walker->getViolations()));
  }
}