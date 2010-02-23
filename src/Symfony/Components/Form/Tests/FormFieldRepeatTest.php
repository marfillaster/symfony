<?php

namespace Symfony\Components\Form\Tests;

require_once __DIR__ . '/TestInit.php';

use Symfony\Components\Form\FormField;
use Symfony\Components\Form\FormFieldRepeat;
use Symfony\Components\Validator\PassValidator;


class FormFieldRepeatTest_FormField extends FormField
{
  protected function configure()
  {
    $this->setValidator(new PassValidator());
  }
}

class FormFieldRepeatTest_CallableFormField extends FormField {}


class FormFieldRepeatTest extends \PHPUnit_Framework_TestCase
{
  protected $repeat;

  protected function setUp()
  {
    $this->repeat = new FormFieldRepeat('emails', __NAMESPACE__ . '\FormFieldRepeatTest_FormField');
  }

  public function testContainsNoFieldsByDefault()
  {
    $this->assertEquals(0, count($this->repeat));
  }

  public function testSetDefaultAdjustsSize()
  {
    $this->repeat->setDefault(array('foo@foo.com', 'foo@bar.com'));

    $this->assertTrue($this->repeat[0] instanceof FormFieldRepeatTest_FormField);
    $this->assertTrue($this->repeat[1] instanceof FormFieldRepeatTest_FormField);
    $this->assertEquals(2, count($this->repeat));
  }

  public function testSetDefaultRequiresArray()
  {
    $this->setExpectedException('InvalidArgumentException');
    $this->repeat->setDefault('foobar');
  }

  public function testConstructRequiresValidClassName()
  {
    $this->setExpectedException('LogicException');

    new FormFieldRepeat('emails', 'FoobarClass');
  }

  public function testConstructRequiresClassImplementingFormFieldInterface()
  {
    $this->setExpectedException('LogicException');

    new FormFieldRepeat('emails', 'stdClass');
  }

  public function callbackCreateField($key)
  {
    return new FormFieldRepeatTest_CallableFormField($key);
  }

  public function testConstructAcceptsCallables()
  {
    $repeat = new FormFieldRepeat('emails', array($this, 'callbackCreateField'));
    $repeat->setDefault(array('foo@bar.com'));

    $this->assertTrue($repeat[0] instanceof FormFieldRepeatTest_CallableFormField);
  }

  public function testConstructRequiresClassOrCallable()
  {
    $this->setExpectedException('InvalidArgumentException');

    new FormFieldRepeat('emails', 1234);
  }

  public function testModifiableRepeatsContainExtraField()
  {
    $repeat = new FormFieldRepeat('emails', __NAMESPACE__ . '\FormFieldRepeatTest_FormField', array(
      'modifiable' => true,
    ));
    $repeat->setDefault(array('foo@bar.com'));

    $this->assertTrue($repeat['0'] instanceof FormFieldRepeatTest_FormField);
    $this->assertTrue($repeat['$$key$$'] instanceof FormFieldRepeatTest_FormField);
    $this->assertEquals(2, count($repeat));
  }

  public function testNotResizedIfBoundWithMissingData()
  {
    $repeat = new FormFieldRepeat('emails', __NAMESPACE__ . '\FormFieldRepeatTest_FormField');
    $repeat->setDefault(array('foo@foo.com', 'bar@bar.com'));
    $repeat->bind(array('foo@bar.com'));

    $this->assertTrue($repeat->has('0'));
    $this->assertTrue($repeat->has('1'));
  }

  public function testResizedIfBoundWithMissingDataAndModifiable()
  {
    $repeat = new FormFieldRepeat('emails', __NAMESPACE__ . '\FormFieldRepeatTest_FormField', array(
      'modifiable' => true,
    ));
    $repeat->setDefault(array('foo@foo.com', 'bar@bar.com'));
    $repeat->bind(array('foo@bar.com'));

    $this->assertTrue($repeat->has('0'));
    $this->assertFalse($repeat->has('1'));
  }

  public function testNotResizedIfBoundWithExtraData()
  {
    $repeat = new FormFieldRepeat('emails', __NAMESPACE__ . '\FormFieldRepeatTest_FormField');
    $repeat->setDefault(array('foo@bar.com'));
    $repeat->bind(array('foo@foo.com', 'bar@bar.com'));

    $this->assertTrue($repeat->has('0'));
    $this->assertFalse($repeat->has('1'));
  }

  public function testResizedIfBoundWithExtraDataAndModifiable()
  {
    $repeat = new FormFieldRepeat('emails', __NAMESPACE__ . '\FormFieldRepeatTest_FormField', array(
      'modifiable' => true,
    ));
    $repeat->setDefault(array('foo@bar.com'));
    $repeat->bind(array('foo@foo.com', 'bar@bar.com'));

    $this->assertTrue($repeat->has('0'));
    $this->assertTrue($repeat->has('1'));
  }
}
