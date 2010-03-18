<?php

namespace Symfony\Tests\Components\Form;

require_once __DIR__ . '/../../bootstrap.php';;

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\AndValidator;

use Symfony\Components\Form\Field;
use Symfony\Components\Form\FieldInterface;
use Symfony\Components\Form\FieldGroup;

use Symfony\Components\I18N\Localizable;


class FieldGroupTest_Object
{
  private $privateProperty;

  public $firstName;
  private $lastName;
  private $australian;

  public function setLastName($lastName)
  {
    $this->lastName = $lastName;
  }

  public function getLastName()
  {
    return $this->lastName;
  }

  public function setAustralian($australian)
  {
    $this->australian = $australian;
  }

  public function isAustralian()
  {
    return $this->australian;
  }

  public function getPrivateProperty()
  {
    return 'foobar';
  }

  private function setPrivateSetter($value)
  {
  }

  public function getPrivateSetter()
  {
    return 'foobar';
  }

  public function getNoSetter()
  {
    return 'foobar';
  }
}

abstract class FieldGroupTest_LocalizableField implements FieldInterface, Localizable
{
  public $locales = array();

  public function setLocale($locale)
  {
    $this->locales[] = $locale;
  }
}


class FieldGroupTest extends \PHPUnit_Framework_TestCase
{
  protected $object;
  protected $group;

  protected function setUp()
  {
    $this->object = new FieldGroupTest_Object();

    $this->group = new FieldGroup('author');
    $this->group->initialize($this->object);
    $this->group->add($this->createMockField('firstName'));
  }

  public function testSupportsArrayAccess()
  {
    $this->assertEquals($this->group->get('firstName'), $this->group['firstName']);
    $this->assertTrue(isset($this->group['firstName']));
  }

  public function testSupportsUnset()
  {
    unset($this->group['firstName']);
    $this->assertFalse(isset($this->group['firstName']));
  }

  public function testDoesNotSupportAddingFields()
  {
    $this->setExpectedException('LogicException');
    $this->group[] = $this->createMockField('lastName');
  }

  public function testSupportsCountable()
  {
    $group = new FieldGroup('group');
    $group->initialize(new FieldGroupTest_Object());
    $group->add($this->createMockField('firstName'));
    $group->add($this->createMockField('lastName'));
    $this->assertEquals(2, count($group));

    $group->add($this->createMockField('australian'));
    $this->assertEquals(3, count($group));
  }

  public function testSupportsIterable()
  {
    $group = new FieldGroup('group');
    $group->add($field1 = $this->createMockField('field1'));
    $group->add($field2 = $this->createMockField('field2'));
    $group->add($field3 = $this->createMockField('field3'));

    $expected = array(
      'field1' => $field1,
      'field2' => $field2,
      'field3' => $field3,
    );

    $this->assertEquals($expected, iterator_to_array($group));
  }

  public function testIsBound()
  {
    $this->assertFalse($this->group->isBound());
    $this->group->bind(array('firstName' => 'Bernhard'));
    $this->assertTrue($this->group->isBound());
  }

  public function testValidIfAllFieldsAreValid()
  {
    $group = new FieldGroup('author');
    $group->initialize($this->object);
    $group->add($this->createValidMockField('firstName'));
    $group->add($this->createValidMockField('lastName'));

    $group->bind(array('firstName' => 'Bernhard', 'lastName' => 'Potencier'));

    $this->assertTrue($group->isValid());
  }

  public function testInvalidIfFieldIsInvalid()
  {
    $group = new FieldGroup('author');
    $group->initialize($this->object);
    $group->add($this->createInvalidMockField('firstName'));
    $group->add($this->createValidMockField('lastName'));

    $group->bind(array('firstName' => 'Bernhard', 'lastName' => 'Potencier'));

    $this->assertFalse($group->isValid());
  }

  public function testInvalidIfBoundWithExtraFields()
  {
    $group = new FieldGroup('author');
    $group->initialize($this->object);
    $group->add($this->createValidMockField('firstName'));
    $group->add($this->createValidMockField('lastName'));

    $group->bind(array('foo' => 'bar', 'firstName' => 'Bernhard', 'lastName' => 'Potencier'));

    $this->assertFalse($group->isValid());
  }

  public function testBindForwardsBoundValues()
  {
    $field = $this->createMockField('firstName');
    $field->expects($this->once())
          ->method('bind')
          ->with($this->equalTo('Bernhard'));

    $group = new FieldGroup('author');
    $group->initialize($this->object);
    $group->add($field);

    $group->bind(array('firstName' => 'Bernhard'));
  }

  public function testBindUpdatesArrayElement()
  {
    $field = $this->createMockField('firstName');
    $field->expects($this->once())
          ->method('getData')
          ->will($this->returnValue('Bernhard'));

    $group = new FieldGroup('author');
    $group->initialize(array());
    $group->add($field);

    $group->bind(array()); // irrelevant

    $data = $group->getData();

    $this->assertEquals('Bernhard', $data['firstName']);
  }

  public function testBindUpdatesProperties()
  {
    $field = $this->createMockField('firstName');
    $field->expects($this->once())
          ->method('getData')
          ->will($this->returnValue('Bernhard'));

    $object = new FieldGroupTest_Object();

    $group = new FieldGroup('author');
    $group->initialize($object);
    $group->add($field);

    $group->bind(array()); // irrelevant

    $this->assertEquals('Bernhard', $object->firstName);
  }

  public function testBindUpdatesSetters()
  {
    $field = $this->createMockField('lastName');
    $field->expects($this->once())
          ->method('getData')
          ->will($this->returnValue('Schussek'));

    $object = new FieldGroupTest_Object();

    $group = new FieldGroup('author');
    $group->initialize($object);
    $group->add($field);

    $group->bind(array()); // irrelevant

    $this->assertEquals('Schussek', $object->getLastName());
  }

  public function testBindThrowsExceptionIfPropertyIsNotPublic()
  {
    $object = new FieldGroupTest_Object();

    $group = new FieldGroup('author');
    $group->initialize($object);
    $group->add($this->createMockField('privateProperty'));

    $this->setExpectedException('Symfony\Components\Form\Exception\PropertyAccessDeniedException');
    $group->bind(array()); // irrelevant
  }

  public function testBindThrowsExceptionIfSetterIsNotPublic()
  {
    $object = new FieldGroupTest_Object();

    $group = new FieldGroup('author');
    $group->initialize($object);
    $group->add($this->createMockField('privateSetter'));

    $this->setExpectedException('Symfony\Components\Form\Exception\PropertyAccessDeniedException');
    $group->bind(array()); // irrelevant
  }

  public function testBindThrowsExceptionIfPropertyDoesNotExist()
  {
    $object = new FieldGroupTest_Object();

    $group = new FieldGroup('author');
    $group->initialize($object);
    $group->add($this->createMockField('noSetter'));

    $this->setExpectedException('Symfony\Components\Form\Exception\InvalidPropertyException');
    $group->bind(array()); // irrelevant
  }

  public function testBindForwardsNullIfFieldIsMissing()
  {
    $field = $this->createMockField('firstName');
    $field->expects($this->once())
          ->method('bind')
          ->with($this->equalTo(null));

    $group = new FieldGroup('author');
    $group->initialize($this->object);
    $group->add($field);

    $group->bind(array());
  }

  public function testAddThrowsExceptionIfAlreadyBound()
  {
    $this->group->bind(array('firstName' => 'Bernhard'));

    $this->setExpectedException('Symfony\Components\Form\Exception\AlreadyBoundException');
    $this->group->add($this->createMockField('lastName'));
  }

  public function testMergeAddsAnotherGroup()
  {
    $group1 = new FieldGroup('author');
    $group1->add($field1 = $this->createMockField('firstName'));

    $group2 = new FieldGroup('publisher');
    $group2->add($field2 = $this->createMockField('lastName'));

    $group1->merge($group2);

    $this->assertEquals($group2, $group1->get('publisher'));
  }

  public function testMergeThrowsExceptionIfOtherGroupAlreadyBound()
  {
    $group1 = new FieldGroup('author');
    $group2 = new FieldGroup('publisher');
    $group2->initialize($this->object);
    $group2->add($this->createMockField('firstName'));

    $group2->bind(array('firstName' => 'Bernhard'));

    $this->setExpectedException('Symfony\Components\Form\Exception\AlreadyBoundException');
    $group1->merge($group2);
  }

  public function testBindUpdatesMergedProperties()
  {
    $field = $this->createMockField('firstName');
    $field->expects($this->once())
          ->method('getData')
          ->will($this->returnValue('Bernhard'));

    $object = new FieldGroupTest_Object();

    $group1 = new FieldGroup('author');
    $group1->initialize($object);
    $group2 = new FieldGroup('nested');
    $group2->add($field);

    $group1->merge($group2);
    $group1->bind(array()); // irrelevant

    $this->assertEquals('Bernhard', $object->firstName);
  }


  public function testGetDataReturnsObject()
  {
    $this->assertEquals($this->object, $this->group->getData());
  }

  public function testGetDisplayedDataForwardsCall()
  {
    $field = $this->createValidMockField('firstName');
    $field->expects($this->atLeastOnce())
          ->method('getDisplayedData')
          ->will($this->returnValue('Bernhard'));

    $group = new FieldGroup('author');
    $group->add($field);

    $this->assertEquals(array('firstName' => 'Bernhard'), $group->getDisplayedData());
  }

  public function testIsMultipartIfAnyFieldIsMultipart()
  {
    $group = new FieldGroup('author');
    $group->add($this->createMultipartMockField('firstName'));
    $group->add($this->createNonMultipartMockField('lastName'));

    $this->assertTrue($group->isMultipart());
  }

  public function testIsNotMultipartIfNoFieldIsMultipart()
  {
    $group = new FieldGroup('author');
    $group->add($this->createNonMultipartMockField('firstName'));
    $group->add($this->createNonMultipartMockField('lastName'));

    $this->assertFalse($group->isMultipart());
  }

  public function testLocaleIsPassedToLocalizableField_SetBeforeAddingTheField()
  {
    $field = $this->getMock(__NAMESPACE__ . '\LocalizableField', array(), array(), '', false, false);
    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue('firstName'));
    $field->expects($this->once())
          ->method('setLocale')
          ->with($this->equalTo('de_DE'));

    $this->group->setLocale('de_DE');
    $this->group->add($field);
  }

  public function testLocaleIsPassedToLocalizableField_SetAfterAddingTheField()
  {
    $field = $this->getMockForAbstractClass(__NAMESPACE__ . '\FieldGroupTest_LocalizableField', array(), '', false, false);
    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue('firstName'));
// DOESN'T WORK!
//    $field = $this->getMock(__NAMESPACE__ . '\LocalizableField', array(), array(), '', false, false);
//    $field->expects($this->once())
//          ->method('setLocale')
//          ->with($this->equalTo('de_AT'));
//    $field->expects($this->once())
//          ->method('setLocale')
//          ->with($this->equalTo('de_DE'));

    $this->group->add($field);
    $this->group->setLocale('de_DE');

    $this->assertEquals(array(\Locale::getDefault(), 'de_DE'), $field->locales);
  }

  public function testTranslatorIsPassedToTranslatableField_SetBeforeAddingTheField()
  {
    $translator = $this->getMock('Symfony\Components\I18N\TranslatorInterface');
    $field = $this->getMock(__NAMESPACE__ . '\TranslatableField', array(), array(), '', false, false);
    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue('firstName'));
    $field->expects($this->once())
          ->method('setTranslator')
          ->with($this->equalTo($translator));

    $this->group->setTranslator($translator);
    $this->group->add($field);
  }

  public function testTranslatorIsPassedToTranslatableField_SetAfterAddingTheField()
  {
    $translator = $this->getMock('Symfony\Components\I18N\TranslatorInterface');
    $field = $this->getMock(__NAMESPACE__ . '\TranslatableField', array(), array(), '', false, false);
    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue('firstName'));
    $field->expects($this->once())
          ->method('setTranslator')
          ->with($this->equalTo($translator));

    $this->group->add($field);
    $this->group->setTranslator($translator);
  }

  public function testTranslatorIsNotPassedToFieldIfNotSet()
  {
    $field = $this->getMock(__NAMESPACE__ . '\TranslatableField', array(), array(), '', false, false);
    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue('firstName'));
    $field->expects($this->never())
          ->method('setTranslator');

    $this->group->add($field);
  }

  public function testSupportsClone()
  {
    $clone = clone $this->group;

    $this->assertNotSame($clone['firstName'], $this->group['firstName']);
  }

  protected function createMockField($key)
  {
    $field = $this->getMock(
      'Symfony\Components\Form\FieldInterface',
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

  protected function createInvalidMockField($key)
  {
    $field = $this->createMockField($key);
    $field->expects($this->any())
          ->method('isValid')
          ->will($this->returnValue(false));

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

  protected function createNonMultipartMockField($key)
  {
    $field = $this->createMockField($key);
    $field->expects($this->any())
          ->method('isMultipart')
          ->will($this->returnValue(false));

    return $field;
  }

  protected function createMultipartMockField($key)
  {
    $field = $this->createMockField($key);
    $field->expects($this->any())
          ->method('isMultipart')
          ->will($this->returnValue(true));

    return $field;
  }

  protected function createMockValidator()
  {
    return $this->getMock('Symfony\Components\Validator\ValidatorInterface');
  }
}
