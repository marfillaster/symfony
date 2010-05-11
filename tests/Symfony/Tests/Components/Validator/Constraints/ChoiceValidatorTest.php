<?php

namespace Symfony\Tests\Components\Validator;

require_once __DIR__.'/../../../bootstrap.php';

use Symfony\Components\Validator\ValidationContext;
use Symfony\Components\Validator\Constraints\Choice;
use Symfony\Components\Validator\Constraints\ChoiceValidator;

function choice_callback()
{
  return array('foo', 'bar');
}

class ChoiceValidatorTest extends \PHPUnit_Framework_TestCase
{
  protected $validator;

  public static function staticCallback()
  {
    return array('foo', 'bar');
  }

  public function setUp()
  {
    $context = new ValidationContext('root');
    $context->setCurrentClass(__CLASS__);
    $this->validator = new ChoiceValidator();
    $this->validator->initialize($context);
  }

  public function testNullIsValid()
  {
    $this->assertTrue($this->validator->isValid(null, new Choice(array('choices' => array('foo', 'bar')))));
  }

  public function testChoicesOrCallbackExpected()
  {
    $this->setExpectedException('Symfony\Components\Validator\Exception\ConstraintDefinitionException');

    $this->validator->isValid('foobar', new Choice());
  }

  public function testValidCallbackExpected()
  {
    $this->setExpectedException('Symfony\Components\Validator\Exception\ConstraintDefinitionException');

    $this->validator->isValid('foobar', new Choice(array('callback' => 'abcd')));
  }

  public function testValidChoiceArray()
  {
    $constraint = new Choice(array('choices' => array('foo', 'bar')));

    $this->assertTrue($this->validator->isValid('bar', $constraint));
  }

  public function testValidChoiceCallbackFunction()
  {
    $constraint = new Choice(array('callback' => __NAMESPACE__.'\choice_callback'));

    $this->assertTrue($this->validator->isValid('bar', $constraint));
  }

  public function testValidChoiceCallbackClosure()
  {
    $constraint = new Choice(array('callback' => function() {
      return array('foo', 'bar');
    }));

    $this->assertTrue($this->validator->isValid('bar', $constraint));
  }

  public function testValidChoiceCallbackStaticMethod()
  {
    $constraint = new Choice(array('callback' => array(__CLASS__, 'staticCallback')));

    $this->assertTrue($this->validator->isValid('bar', $constraint));
  }

  public function testValidChoiceCallbackContextMethod()
  {
    $constraint = new Choice(array('callback' => 'staticCallback'));

    $this->assertTrue($this->validator->isValid('bar', $constraint));
  }

  public function testMultipleChoices()
  {
    $constraint = new Choice(array(
      'choices' => array('foo', 'bar', 'baz'),
      'multiple' => true,
    ));

    $this->assertTrue($this->validator->isValid(array('baz', 'bar'), $constraint));
  }

  public function testInvalidChoice()
  {
    $constraint = new Choice(array(
      'choices' => array('foo', 'bar'),
      'message' => 'myMessage',
    ));

    $this->assertFalse($this->validator->isValid('baz', $constraint));
    $this->assertEquals($this->validator->getMessageTemplate(), 'myMessage');
    $this->assertEquals($this->validator->getMessageParameters(), array(
      'value' => 'baz',
    ));
  }

  public function testInvalidChoiceMultiple()
  {
    $constraint = new Choice(array(
      'choices' => array('foo', 'bar'),
      'message' => 'myMessage',
      'multiple' => true,
    ));

    $this->assertFalse($this->validator->isValid(array('foo', 'baz'), $constraint));
    $this->assertEquals($this->validator->getMessageTemplate(), 'myMessage');
    $this->assertEquals($this->validator->getMessageParameters(), array(
      'value' => 'baz',
    ));
  }

  public function testTooFewChoices()
  {
    $constraint = new Choice(array(
      'choices' => array('foo', 'bar', 'moo', 'maa'),
      'multiple' => true,
      'min' => 2,
      'minMessage' => 'myMessage',
    ));

    $this->assertFalse($this->validator->isValid(array('foo'), $constraint));
    $this->assertEquals($this->validator->getMessageTemplate(), 'myMessage');
    $this->assertEquals($this->validator->getMessageParameters(), array(
      'limit' => 2,
    ));
  }

  public function testTooManyChoices()
  {
    $constraint = new Choice(array(
      'choices' => array('foo', 'bar', 'moo', 'maa'),
      'multiple' => true,
      'max' => 2,
      'maxMessage' => 'myMessage',
    ));

    $this->assertFalse($this->validator->isValid(array('foo', 'bar', 'moo'), $constraint));
    $this->assertEquals($this->validator->getMessageTemplate(), 'myMessage');
    $this->assertEquals($this->validator->getMessageParameters(), array(
      'limit' => 2,
    ));
  }
}