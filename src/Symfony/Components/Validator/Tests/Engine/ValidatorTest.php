<?php

namespace Symfony\Components\Validator\Tests\Engine;

require_once __DIR__.'/../TestInit.php';


use Symfony\Components\Validator\Engine\Validator;
use Symfony\Components\Validator\Engine\ConstraintViolation;
use Symfony\Components\Validator\Engine\ConstraintViolationList;
use Symfony\Components\Validator\Engine\PropertyPathBuilder;
use Symfony\Components\Validator\Specification\ConstraintSpecification;
use Symfony\Components\Validator\Specification\PropertySpecification;
use Symfony\Components\Validator\Specification\ClassSpecification;
use Symfony\Components\Validator\Specification\Specification;


class ValidatorTest_Class
{
  public $firstName = 'Bernhard';

  public $reference;

  public function getLastName()
  {
    return 'Schussek';
  }

  public function isAustralian()
  {
    return false;
  }
}


class ValidatorTest extends \PHPUnit_Framework_TestCase
{
  public function testValidatePropertyConstraint()
  {
    $subject = new ValidatorTest_Class();
    $subjectClass = get_class($subject);

    $constraint = new ConstraintSpecification('Constraint');
    $property = new PropertySpecification($subjectClass, 'firstName', array($constraint));
    $class = new ClassSpecification($subjectClass, array($property));
    $specification = new Specification(array($class));

    $validatorMock = $this->getMock('Symfony\Components\Validator\ConstraintValidatorInterface');
    $validatorMock->expects($this->once())
                  ->method('validate')
                  ->with($this->equalTo('Bernhard'))
                  ->will($this->returnValue(false));
    $validatorMock->expects($this->atLeastOnce())
                  ->method('getMessageTemplate')
                  ->will($this->returnValue('message'));
    $validatorMock->expects($this->atLeastOnce())
                  ->method('getMessageParameters')
                  ->will($this->returnValue(array('param' => 'value')));

    $factoryMock = $this->getMock('Symfony\Components\Validator\ConstraintValidatorFactoryInterface');
    $factoryMock->expects($this->once())
                ->method('getValidator')
                ->with($this->equalTo('Constraint'))
                ->will($this->returnValue($validatorMock));

    $validator = new Validator($specification, $factoryMock);

    $builder = new PropertyPathBuilder();
    $expected = new ConstraintViolationList();
    $expected->add(new ConstraintViolation(
      'message',
      array('param' => 'value'),
      $subjectClass,
      $builder->atProperty('firstName')->getPropertyPath(),
      'Bernhard'
    ));

    $this->assertEquals($expected, $validator->validateProperty($subject, 'firstName'));
  }
}