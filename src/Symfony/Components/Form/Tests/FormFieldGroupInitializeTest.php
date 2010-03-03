<?php

namespace Symfony\Components\Form\Tests;

require_once __DIR__ . '/TestInit.php';

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\AndValidator;

use Symfony\Components\Form\FormField;
use Symfony\Components\Form\FormFieldInterface;
use Symfony\Components\Form\FormFieldGroup;


class FormFieldGroupInitializeTest_Object
{
  public $firstName;
  private $lastName;
  private $australian;

  private $privateProperty;

  public function setLastName($lastName)
  {
    $this->lastName = $lastName;
  }

  public function getLastName()
  {
    return $this->lastName;
  }

  private function getPrivateGetter()
  {
    return 'foobar';
  }

  public function setAustralian($australian)
  {
    $this->australian = $australian;
  }

  public function isAustralian()
  {
    return $this->australian;
  }

  private function isPrivateIsser()
  {
    return true;
  }
}


class FormFieldGroupInitializeTest extends \PHPUnit_Framework_TestCase
{
  public function testInitializeRequiresAnObject()
  {
    $group = new FormFieldGroup('author');

    $this->setExpectedException('Symfony\Components\Form\Exception\UnexpectedTypeException');
    $group->initialize('foobar');
  }

  public function testInitializeGroup()
  {
    $object = new FormFieldGroupInitializeTest_Object();
    $group1 = new FormFieldGroup('author');
    $group1->initialize($object);

    $this->assertEquals($object, $group1->getData());
  }

  public function testInitializeMergedGroup_CalledBeforeMerging()
  {
    $object = new FormFieldGroupInitializeTest_Object();

    $group1 = new FormFieldGroup('author');
    $group2 = new FormFieldGroup('publisher');

    $group1->initialize($object);
    $group1->merge($group2);

    $this->assertEquals($object, $group2->getData());
  }

  public function testInitializeMergedGroup_CalledAfterMerging()
  {
    $object = new FormFieldGroupInitializeTest_Object();

    $group1 = new FormFieldGroup('author');
    $group2 = new FormFieldGroup('publisher');

    $group1->merge($group2);
    $group1->initialize($object);

    $this->assertEquals($object, $group2->getData());
  }

  public function testInitializeReadsProperties_CalledBeforeAddingTheField()
  {
    $object = new FormFieldGroupInitializeTest_Object();
    $object->firstName = 'Bernhard';

    $field = $this->createValidMockField('firstName');
    $field->expects($this->once())
          ->method('initialize')
          ->with($this->equalTo('Bernhard'));

    $group = new FormFieldGroup('author');
    $group->initialize($object);
    $group->add($field);
  }

  public function testInitializeReadsProperties_CalledAfterAddingTheField()
  {
    $object = new FormFieldGroupInitializeTest_Object();
    $object->firstName = 'Bernhard';

    $field = $this->createValidMockField('firstName');
    $field->expects($this->once())
          ->method('initialize')
          ->with($this->equalTo('Bernhard'));

    $group = new FormFieldGroup('author');
    $group->add($field);
    $group->initialize($object);
  }

  public function testInitializeThrowsExceptionIfPropertyIsNotPublic()
  {
    $group = new FormFieldGroup('author');
    $group->add($this->createValidMockField('privateProperty'));

    $this->setExpectedException('Symfony\Components\Form\Exception\PropertyAccessDeniedException');
    $group->initialize(new FormFieldGroupInitializeTest_Object());
  }

  public function testInitializeReadsGetters()
  {
    $object = new FormFieldGroupInitializeTest_Object();
    $object->setLastName('Schussek');

    $field = $this->createValidMockField('lastName');
    $field->expects($this->once())
          ->method('initialize')
          ->with($this->equalTo('Schussek'));

    $group = new FormFieldGroup('author');
    $group->initialize($object);
    $group->add($field);
  }

  public function testInitializeThrowsExceptionIfGetterIsNotPublic()
  {
    $group = new FormFieldGroup('author');
    $group->add($this->createValidMockField('privateGetter'));

    $this->setExpectedException('Symfony\Components\Form\Exception\PropertyAccessDeniedException');
    $group->initialize(new FormFieldGroupInitializeTest_Object());
  }

  public function testInitializeReadsIssers()
  {
    $object = new FormFieldGroupInitializeTest_Object();
    $object->setAustralian(false);

    $field = $this->createValidMockField('australian');
    $field->expects($this->once())
          ->method('initialize')
          ->with($this->equalTo(false));

    $group = new FormFieldGroup('author');
    $group->initialize($object);
    $group->add($field);
  }

  public function testInitializeThrowsExceptionIfIsserIsNotPublic()
  {
    $group = new FormFieldGroup('author');
    $group->add($this->createValidMockField('privateIsser'));

    $this->setExpectedException('Symfony\Components\Form\Exception\PropertyAccessDeniedException');
    $group->initialize(new FormFieldGroupInitializeTest_Object());
  }

  public function testInitializeThrowsExceptionIfPropertyDoesNotExist()
  {
    $group = new FormFieldGroup('author');
    $group->add($this->createValidMockField('foobar'));

    $this->setExpectedException('Symfony\Components\Form\Exception\InvalidPropertyException');
    $group->initialize(new FormFieldGroupInitializeTest_Object());
  }

  protected function createMockField($key)
  {
    $field = $this->getMock(
      'Symfony\Components\Form\FormFieldInterface',
      array(),
      array(),
      '',
      false, // don't use constructor
      false  // don't call parent::__clone
    );

    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue($key));

    return $field;
  }

  protected function createValidMockField($key)
  {
    $field = $this->createMockField($key);
    $field->expects($this->any())
          ->method('isValid')
          ->will($this->returnValue(true));

    return $field;
  }
}
