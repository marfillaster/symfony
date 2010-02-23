<?php

namespace Symfony\Components\Form\Tests;

require_once __DIR__ . '/TestInit.php';

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\AndValidator;

use Symfony\Components\Form\FormField;
use Symfony\Components\Form\FormFieldInterface;
use Symfony\Components\Form\FormFieldGroup;

use Symfony\Components\I18N\Localizable;


abstract class FormFieldGroupTest_LocalizableField implements FormFieldInterface, Localizable
{
  public $locales = array();

  public function setLocale($locale)
  {
    $this->locales[] = $locale;
  }
}


class FormFieldGroupTest extends \PHPUnit_Framework_TestCase
{
  protected $group;

  protected function setUp()
  {
    $this->group = new FormFieldGroup('author');
    $this->group->add($this->createMockField('first_name'));
  }

  public function testSupportsArrayAccess()
  {
    $this->assertEquals($this->group->get('first_name'), $this->group['first_name']);
    $this->assertTrue(isset($this->group['first_name']));
  }

  public function testSupportsUnset()
  {
    unset($this->group['first_name']);
    $this->assertFalse(isset($this->group['first_name']));
  }

  public function testDoesNotSupportAddingFields()
  {
    $this->setExpectedException('LogicException');
    $this->group[] = $this->createMockField('last_name');
  }

  public function testSupportsCountable()
  {
    $group = new FormFieldGroup('group');
    $group->add($this->createMockField('field1'));
    $group->add($this->createMockField('field2'));
    $this->assertEquals(2, count($group));

    $group->add($this->createMockField('field3'));
    $this->assertEquals(3, count($group));
  }

  public function testSupportsIterable()
  {
    $group = new FormFieldGroup('group');
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
    $this->group->bind(array('first_name' => 'Fabien'));
    $this->assertTrue($this->group->isBound());
  }

  public function testValidIfAllFieldsAreValid()
  {
    $group = new FormFieldGroup('author');
    $group->add($this->createValidMockField('first_name'));
    $group->add($this->createValidMockField('last_name'));

    $group->bind(array('first_name' => 'Fabien', 'last_name' => 'Potencier'));

    $this->assertTrue($group->isValid());
  }

  public function testInvalidIfFieldIsInvalid()
  {
    $group = new FormFieldGroup('author');
    $group->add($this->createInvalidMockField('first_name'));
    $group->add($this->createValidMockField('last_name'));

    $group->bind(array('first_name' => 'Fabien', 'last_name' => 'Potencier'));

    $this->assertFalse($group->isValid());
  }

  public function testInvalidIfBoundWithExtraFields()
  {
    $group = new FormFieldGroup('author');
    $group->add($this->createValidMockField('first_name'));
    $group->add($this->createValidMockField('last_name'));

    $group->bind(array('foo' => 'bar', 'first_name' => 'Fabien', 'last_name' => 'Potencier'));

    $this->assertFalse($group->isValid());
  }

  public function testBindForwardsBoundValues()
  {
    $field = $this->createMockField('first_name');
    $field->expects($this->once())
          ->method('bind')
          ->with($this->equalTo('Fabien'));

    $group = new FormFieldGroup('author');
    $group->add($field);

    $group->bind(array('first_name' => 'Fabien'));
  }

  public function testBindForwardsNullIfFieldIsMissing()
  {
    $field = $this->createMockField('first_name');
    $field->expects($this->once())
          ->method('bind')
          ->with($this->equalTo(null));

    $group = new FormFieldGroup('author');
    $group->add($field);

    $group->bind(array());
  }

  public function testGetDataReturnsEmptyArrayIfNotBound()
  {
    $this->assertEquals(array(), $this->group->getData());
  }

  public function testGetDataReturnsEmptyArrayIfInvalid()
  {
    $field = $this->createInvalidMockField('first_name');

    $group = new FormFieldGroup('author');
    $group->add($field);
    $group->bind(array('first_name' => '')); // field is mocked anyway

    $this->assertEquals(array(), $this->group->getData());
  }

  public function testGetDataForwardsCallIfBound()
  {
    $field = $this->createValidMockField('first_name');
    $field->expects($this->atLeastOnce())
          ->method('getData')
          ->will($this->returnValue('Fabien'));

    $group = new FormFieldGroup('author');
    $group->add($field);
    $group->bind(array('first_name' => '')); // field is mocked anyway

    $this->assertEquals(array('first_name' => 'Fabien'), $group->getData());
  }

  public function testGetDisplayedDataForwardsCall()
  {
    $field = $this->createValidMockField('first_name');
    $field->expects($this->atLeastOnce())
          ->method('getDisplayedData')
          ->will($this->returnValue('Fabien'));

    $group = new FormFieldGroup('author');
    $group->add($field);

    $this->assertEquals(array('first_name' => 'Fabien'), $group->getDisplayedData());
  }

  public function testSetDefaultRequiresAnArray()
  {
    $this->setExpectedException('InvalidArgumentException');
    $this->group->setDefault('foobar');
  }

  public function testSetDefaultForwardsCall()
  {
    $field = $this->createValidMockField('first_name');
    $field->expects($this->once())
          ->method('setDefault')
          ->with($this->equalTo('Fabien'));

    $group = new FormFieldGroup('author');
    $group->add($field);

    $group->setDefault(array('first_name' => 'Fabien'));
  }

  public function testIsMultipartIfAnyFieldIsMultipart()
  {
    $group = new FormFieldGroup('author');
    $group->add($this->createMultipartMockField('first_name'));
    $group->add($this->createNonMultipartMockField('last_name'));

    $this->assertTrue($group->isMultipart());
  }

  public function testIsNotMultipartIfNoFieldIsMultipart()
  {
    $group = new FormFieldGroup('author');
    $group->add($this->createNonMultipartMockField('first_name'));
    $group->add($this->createNonMultipartMockField('last_name'));

    $this->assertFalse($group->isMultipart());
  }

  public function testPreValidatorReceivesBoundData()
  {
    $validator = $this->createMockValidator();
    $validator->expects($this->once())
              ->method('validate')
              ->with($this->equalTo(array('first_name' => 'data')));

    $field = $this->createValidMockField('first_name');
    $field->expects($this->atLeastOnce())
          ->method('getData')
          ->will($this->returnValue('transformed[data]'));

    $group = new FormFieldGroup('author');
    $group->add($field);
    $group->setPreValidator($validator);

    // test
    $group->bind(array('first_name' => 'data'));
  }

  public function testPostValidatorReceivesNormalizedData()
  {
    $validator = $this->createMockValidator();
    $validator->expects($this->once())
              ->method('validate')
              ->with($this->equalTo(array('first_name' => 'transformed[data]')));

    $field = $this->createValidMockField('first_name');
    $field->expects($this->atLeastOnce())
          ->method('getData')
          ->will($this->returnValue('transformed[data]'));

    $group = new FormFieldGroup('author');
    $group->add($field);
    $group->setPostValidator($validator);

    // test
    $group->bind(array('first_name' => 'data'));
  }

  public function testMergeFieldsFromAnotherGroup()
  {
    $group1 = new FormFieldGroup('author');
    $group1->add($field1 = $this->createMockField('first_name'));

    $group2 = new FormFieldGroup('publisher');
    $group2->add($field2 = $this->createMockField('last_name'));

    $group1->merge($group2);

    $this->assertEquals($field1, $group1->get('first_name'));
    $this->assertEquals($field2, $group1->get('last_name'));
    $this->assertEquals(2, count($group1));
  }

  public function testMergePreValidators()
  {
    $group1 = new FormFieldGroup('author');
    $group1->setPreValidator($validator1 = $this->createMockValidator());

    $group2 = new FormFieldGroup('publisher');
    $group2->setPreValidator($validator2 = $this->createMockValidator());

    $group1->merge($group2);

    $expected = new AndValidator(array($validator1, $validator2));

    $this->assertEquals($expected, $group1->getPreValidator());
  }

  public function testMergePostValidators()
  {
    $group1 = new FormFieldGroup('author');
    $group1->setPostValidator($validator1 = $this->createMockValidator());

    $group2 = new FormFieldGroup('publisher');
    $group2->setPostValidator($validator2 = $this->createMockValidator());

    $group1->merge($group2);

    $expected = new AndValidator(array($validator1, $validator2));

    $this->assertEquals($expected, $group1->getPostValidator());
  }

  public function testMergeThrowsExceptionIfAlreadyBound()
  {
    $group1 = new FormFieldGroup('author');
    $group1->add($this->createMockField('first_name'));
    $group2 = new FormFieldGroup('publisher');

    $group1->bind(array('first_name' => 'Fabien'));

    $this->setExpectedException('Symfony\Components\Form\Exception\AlreadyBoundException');
    $group1->merge($group2);
  }

  public function testMergeThrowsExceptionIfOtherGroupAlreadyBound()
  {
    $group1 = new FormFieldGroup('author');
    $group2 = new FormFieldGroup('publisher');
    $group2->add($this->createMockField('first_name'));

    $group2->bind(array('first_name' => 'Fabien'));

    $this->setExpectedException('Symfony\Components\Form\Exception\AlreadyBoundException');
    $group1->merge($group2);
  }

  public function testLocaleIsPassedToLocalizableField_SetBeforeAddingTheField()
  {
    $field = $this->getMock(__NAMESPACE__ . '\LocalizableField', array(), array(), '', false, false);
    $field->expects($this->once())
          ->method('setLocale')
          ->with($this->equalTo('de_DE'));

    $this->group->setLocale('de_DE');
    $this->group->add($field);
  }

  public function testLocaleIsPassedToLocalizableField_SetAfterAddingTheField()
  {
    $field = $this->getMockForAbstractClass(__NAMESPACE__ . '\FormFieldGroupTest_LocalizableField', array(), '', false, false);
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
    $field->expects($this->once())
          ->method('setTranslator')
          ->with($this->equalTo($translator));

    $this->group->add($field);
    $this->group->setTranslator($translator);
  }

  public function testTranslatorIsNotPassedToFieldIfNotSet()
  {
    $field = $this->getMock(__NAMESPACE__ . '\TranslatableField', array(), array(), '', false, false);
    $field->expects($this->never())
          ->method('setTranslator');

    $this->group->add($field);
  }

  public function testLocaleIsPassedToLocalizablePreValidator()
  {
    $validator = $this->getMock(__NAMESPACE__ . '\LocalizableValidator');
    $validator->expects($this->once())
              ->method('setLocale')
              ->with($this->equalTo('de_DE'));

    $this->group->setPreValidator($validator);
    $this->group->setLocale('de_DE');
    $this->group->bind(array('first_name' => 'Fabien'));
  }

  public function testTranslatorIsPassedToTranslatablePreValidator()
  {
    $translator = $this->getMock('Symfony\Components\I18N\TranslatorInterface');
    $validator = $this->getMock(__NAMESPACE__ . '\TranslatableValidator');
    $validator->expects($this->once())
              ->method('setTranslator')
              ->with($this->equalTo($translator));

    $this->group->setPreValidator($validator);
    $this->group->setTranslator($translator);
    $this->group->bind(array('first_name' => 'Fabien'));
  }

  public function testTranslatorIsNotPassedToPreValidatorIfNotSet()
  {
    $validator = $this->getMock(__NAMESPACE__ . '\TranslatableValidator');
    $validator->expects($this->never())
              ->method('setTranslator');

    $this->group->setPreValidator($validator);
    $this->group->bind(array('first_name' => 'Fabien'));
  }

  public function testLocaleIsPassedToLocalizablePostValidator()
  {
    $validator = $this->getMock(__NAMESPACE__ . '\LocalizableValidator');
    $validator->expects($this->once())
              ->method('setLocale')
              ->with($this->equalTo('de_DE'));

    $this->group->setPostValidator($validator);
    $this->group->setLocale('de_DE');
    $this->group->bind(array('first_name' => 'Fabien'));
  }

  public function testTranslatorIsPassedToTranslatablePostValidator()
  {
    $translator = $this->getMock('Symfony\Components\I18N\TranslatorInterface');
    $validator = $this->getMock(__NAMESPACE__ . '\TranslatableValidator');
    $validator->expects($this->once())
              ->method('setTranslator')
              ->with($this->equalTo($translator));

    $this->group->setPostValidator($validator);
    $this->group->setTranslator($translator);
    $this->group->bind(array('first_name' => 'Fabien'));
  }

  public function testTranslatorIsNotPassedToPostValidatorIfNotSet()
  {
    $validator = $this->getMock(__NAMESPACE__ . '\TranslatableValidator');
    $validator->expects($this->never())
              ->method('setTranslator');

    $this->group->setPostValidator($validator);
    $this->group->bind(array('first_name' => 'Fabien'));
  }

  public function testSupportsClone()
  {
    $clone = clone $this->group;

    $this->assertNotSame($clone['first_name'], $this->group['first_name']);
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
