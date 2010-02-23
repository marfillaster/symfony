<?php

namespace Symfony\Components\Form\Tests;

require_once __DIR__ . '/TestInit.php';

use Symfony\Components\Form\FormField;
use Symfony\Components\Form\FormFieldGroup;
use Symfony\Components\ValueTransformer\ValueTransformerInterface;
use Symfony\Components\Validator\ValidatorError;


class FormFieldTest_TestValueTransformer implements ValueTransformerInterface
{
  public function transform($value)
  {
    return sprintf('transform[%s]', $value);
  }

  public function reverseTransform($value)
  {
    return sprintf('reverse[%s]', $value);
  }
}


class FormFieldTest extends \PHPUnit_Framework_TestCase
{
  protected $field;

  protected function setUp()
  {
    $this->field = new FormField('title');
    $this->field->setDefault('default');
    $this->field->setValidator($this->createMockValidator());
    $this->field->setValueTransformer(new FormFieldTest_TestValueTransformer());
  }

  public function testIsBound()
  {
    $this->assertFalse($this->field->isBound());
    $this->field->bind('symfony');
    $this->assertTrue($this->field->isBound());
  }

  public function testUnboundFieldHasDefaultData()
  {
    $this->assertEquals('default', $this->field->getData());
  }

  public function testUnboundFieldDisplaysDefaultDataTransformed()
  {
    $this->assertEquals('transform[default]', $this->field->getDisplayedData());
  }

  public function testInvalidFieldIsInvalid()
  {
    $this->field->setValidator($this->createFailingMockValidator());
    $this->field->bind('invalid');

    $this->assertFalse($this->field->isValid());
  }

  public function testInvalidFieldHasNoData()
  {
    $this->field->setValidator($this->createFailingMockValidator());
    $this->field->bind('invalid');

    $this->assertEquals(null, $this->field->getData());
  }

  public function testInvalidFieldDisplaysBoundData()
  {
    $this->field->setValidator($this->createFailingMockValidator());
    $this->field->bind('invalid');

    $this->assertEquals('invalid', $this->field->getDisplayedData());
  }

  public function testValidFieldIsValid()
  {
    $this->field->bind('valid');

    $this->assertTrue($this->field->isValid());
  }

  public function testValidFieldHasBoundDataReverseTransformed()
  {
    $this->field->bind('valid');

    $this->assertEquals('reverse[valid]', $this->field->getData());
  }

  public function testValidFieldDisplaysBoundDataTransformed()
  {
    $this->field->bind('valid');

    $this->assertEquals('transform[reverse[valid]]', $this->field->getDisplayedData());
  }

  public function testProcessDataHooksBeforeValidator()
  {
    $field = $this->getMock(
      'Symfony\Components\Form\FormField',
      array('processData'), // only mock processData()
      array('title')
    );

    $field->expects($this->once())
          ->method('processData')
          ->with($this->equalTo('reverse[data]'))
          ->will($this->returnValue('processed[reverse[data]]'));

    $validator = $this->createMockValidator();
    $validator->expects($this->once())
         ->method('validate')
         ->with($this->equalTo('processed[reverse[data]]'));

    $field->setValidator($validator);
    $field->setValueTransformer(new FormFieldTest_TestValueTransformer());

    // test
    $field->bind('data');

    $this->assertEquals('processed[reverse[data]]', $field->getData());
    $this->assertEquals('transform[processed[reverse[data]]]', $field->getDisplayedData());
  }

  public function testBindThrowsExceptionIfNoValidatorIsSet()
  {
    $field = new FormField('name');

    $this->setExpectedException('Symfony\Components\Form\Exception\InvalidConfigurationException');
    $field->bind('symfony');
  }

  public function testLocaleIsPassedToLocalizableValidator()
  {
    $validator = $this->getMock(__NAMESPACE__ . '\LocalizableValidator');
    $validator->expects($this->once())
              ->method('setLocale')
              ->with($this->equalTo('de_DE'));

    $this->field->setValidator($validator);
    $this->field->setLocale('de_DE');
    $this->field->bind('symfony');
  }

  public function testTranslatorIsPassedToTranslatableValidator()
  {
    $translator = $this->getMock('Symfony\Components\I18N\TranslatorInterface');
    $validator = $this->getMock(__NAMESPACE__ . '\TranslatableValidator');
    $validator->expects($this->once())
              ->method('setTranslator')
              ->with($this->equalTo($translator));

    $this->field->setValidator($validator);
    $this->field->setTranslator($translator);
    $this->field->bind('symfony');
  }

  public function testTranslatorIsNotPassedToValidatorIfNotSet()
  {
    $validator = $this->getMock(__NAMESPACE__ . '\TranslatableValidator');
    $validator->expects($this->never())
              ->method('setTranslator');

    $this->field->setValidator($validator);
    $this->field->bind('symfony');
  }

  public function testLocaleIsPassedToLocalizableValueTransformer()
  {
    $transformer = $this->getMock(__NAMESPACE__ . '\LocalizableValueTransformer');
    $transformer->expects($this->once())
                ->method('setLocale')
                ->with($this->equalTo('de_DE'));

    $this->field->setValueTransformer($transformer);
    $this->field->setLocale('de_DE');
    $this->field->bind('symfony');
  }

  public function testTranslatorIsPassedToTranslatableValueTransformer()
  {
    $translator = $this->getMock('Symfony\Components\I18N\TranslatorInterface');
    $transformer = $this->getMock(__NAMESPACE__ . '\TranslatableValueTransformer');
    $transformer->expects($this->once())
                ->method('setTranslator')
                ->with($this->equalTo($translator));

    $this->field->setValueTransformer($transformer);
    $this->field->setTranslator($translator);
    $this->field->bind('symfony');
  }

  public function testTranslatorIsNotPassedToValueTransformerIfNotSet()
  {
    $transformer = $this->getMock(__NAMESPACE__ . '\TranslatableValueTransformer');
    $transformer->expects($this->never())
                ->method('setTranslator');

    $this->field->setValueTransformer($transformer);
    $this->field->bind('symfony');
  }

  protected function createMockValidator()
  {
    return $this->getMock('Symfony\Components\Validator\ValidatorInterface');
  }

  protected function createFailingMockValidator()
  {
    $validator = $this->createMockValidator();
    $validator->expects($this->any())
                    ->method('validate')
                    ->will($this->throwException(new ValidatorError('message')));

    return $validator;
  }
}
