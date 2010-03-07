<?php

namespace Symfony\Components\Form\Tests;

require_once __DIR__ . '/TestInit.php';

use Symfony\Components\Form\Field;
use Symfony\Components\Form\FieldRepeat;
use Symfony\Components\Validator\PassValidator;


class FieldRepeatTest_CallableField extends Field {}


class FieldRepeatTest extends \PHPUnit_Framework_TestCase
{
  protected $repeat;

  protected function setUp()
  {
    $this->repeat = new FieldRepeat('emails', 'Symfony\Components\Form\Field');
  }

  public function testContainsNoFieldsByDefault()
  {
    $this->assertEquals(0, count($this->repeat));
  }

  public function testInitializeAdjustsSize()
  {
    $this->repeat->initialize(array('foo@foo.com', 'foo@bar.com'));

    $this->assertTrue($this->repeat[0] instanceof Field);
    $this->assertTrue($this->repeat[1] instanceof Field);
    $this->assertEquals(2, count($this->repeat));
  }

  public function testThrowsExceptionIfObjectIsNotTraversable()
  {
    $this->setExpectedException('Symfony\Components\Form\Exception\UnexpectedTypeException');
    $this->repeat->initialize(new \stdClass());
  }

  public function testConstructRequiresValidClassName()
  {
    $this->setExpectedException('LogicException');

    new FieldRepeat('emails', 'FoobarClass');
  }

  public function testConstructRequiresClassImplementingFieldInterface()
  {
    $this->setExpectedException('LogicException');

    new FieldRepeat('emails', 'stdClass');
  }

  public function callbackCreateField($key)
  {
    return new FieldRepeatTest_CallableField($key);
  }

  public function testConstructAcceptsCallables()
  {
    $repeat = new FieldRepeat('emails', array($this, 'callbackCreateField'));
    $repeat->initialize(array('foo@bar.com'));

    $this->assertTrue($repeat[0] instanceof FieldRepeatTest_CallableField);
  }

  public function testConstructRequiresClassOrCallable()
  {
    $this->setExpectedException('InvalidArgumentException');

    new FieldRepeat('emails', 1234);
  }

  public function testModifiableRepeatsContainExtraField()
  {
    $repeat = new FieldRepeat('emails', 'Symfony\Components\Form\Field', array(
      'modifiable' => true,
    ));
    $repeat->initialize(array('foo@bar.com'));

    $this->assertTrue($repeat['0'] instanceof Field);
    $this->assertTrue($repeat['$$key$$'] instanceof Field);
    $this->assertEquals(2, count($repeat));
  }

  public function testNotResizedIfBoundWithMissingData()
  {
    $repeat = new FieldRepeat('emails', 'Symfony\Components\Form\Field');
    $repeat->initialize(array('foo@foo.com', 'bar@bar.com'));
    $repeat->bind(array('foo@bar.com'));

    $this->assertTrue($repeat->has('0'));
    $this->assertTrue($repeat->has('1'));
  }

  public function testResizedIfBoundWithMissingDataAndModifiable()
  {
    $repeat = new FieldRepeat('emails', 'Symfony\Components\Form\Field', array(
      'modifiable' => true,
    ));
    $repeat->initialize(array('foo@foo.com', 'bar@bar.com'));
    $repeat->bind(array('foo@bar.com'));

    $this->assertTrue($repeat->has('0'));
    $this->assertFalse($repeat->has('1'));
  }

  public function testNotResizedIfBoundWithExtraData()
  {
    $repeat = new FieldRepeat('emails', 'Symfony\Components\Form\Field');
    $repeat->initialize(array('foo@bar.com'));
    $repeat->bind(array('foo@foo.com', 'bar@bar.com'));

    $this->assertTrue($repeat->has('0'));
    $this->assertFalse($repeat->has('1'));
  }

  public function testResizedIfBoundWithExtraDataAndModifiable()
  {
    $repeat = new FieldRepeat('emails', 'Symfony\Components\Form\Field', array(
      'modifiable' => true,
    ));
    $repeat->initialize(array('foo@bar.com'));
    $repeat->bind(array('foo@foo.com', 'bar@bar.com'));

    $this->assertTrue($repeat->has('0'));
    $this->assertTrue($repeat->has('1'));
  }
}
