<?php

namespace Symfony\Tests\Components\Form;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/Fixtures/LocalizableRenderer.php';
require_once __DIR__ . '/Fixtures/LocalizableValueTransformer.php';
require_once __DIR__ . '/Fixtures/TranslatableRenderer.php';
require_once __DIR__ . '/Fixtures/TranslatableValueTransformer.php';

use Symfony\Components\Form\Field;
use Symfony\Components\Form\FieldGroup;
use Symfony\Components\Form\ValueTransformer\ValueTransformerInterface;


class FieldTest_TestValueTransformer implements ValueTransformerInterface
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


class FieldTest extends \PHPUnit_Framework_TestCase
{
  protected $field;

  protected function setUp()
  {
    $this->field = new Field('title');
    $this->field->initialize('default');
    $this->field->setValueTransformer(new FieldTest_TestValueTransformer());
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

  public function testFieldHasBoundDataReverseTransformed()
  {
    $this->field->bind('data');

    $this->assertEquals('reverse[data]', $this->field->getData());
  }

  public function testFieldDisplaysBoundDataTransformed()
  {
    $this->field->bind('data');

    $this->assertEquals('transform[reverse[data]]', $this->field->getDisplayedData());
  }

  public function testBoundValuesAreConvertedToStrings()
  {
    $transformer = $this->createMockTransformer();
    $transformer->expects($this->once())
                ->method('reverseTransform')
                ->with($this->identicalTo('0'));

    $this->field->setValueTransformer($transformer);
    $this->field->bind(0);
  }

  public function testProcessDataHooksAfterReverseTransformation()
  {
    $field = $this->getMock(
      'Symfony\Components\Form\Field',
      array('processData'), // only mock processData()
      array('title')
    );

    $field->expects($this->once())
          ->method('processData')
          ->with($this->equalTo('reverse[data]'))
          ->will($this->returnValue('processed[reverse[data]]'));

    $field->initialize('default');
    $field->setValueTransformer(new FieldTest_TestValueTransformer());

    // test
    $field->bind('data');

    $this->assertEquals('processed[reverse[data]]', $field->getData());
    $this->assertEquals('transform[processed[reverse[data]]]', $field->getDisplayedData());
  }

  public function testLocaleIsPassedToLocalizableValueTransformer()
  {
    $transformer = $this->getMock(__NAMESPACE__ . '\Fixtures\LocalizableValueTransformer');
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
    $transformer = $this->getMock(__NAMESPACE__ . '\Fixtures\TranslatableValueTransformer');
    $transformer->expects($this->once())
                ->method('setTranslator')
                ->with($this->equalTo($translator));

    $this->field->setValueTransformer($transformer);
    $this->field->setTranslator($translator);
    $this->field->bind('symfony');
  }

  public function testTranslatorIsNotPassedToValueTransformerIfNotSet()
  {
    $transformer = $this->getMock(__NAMESPACE__ . '\Fixtures\TranslatableValueTransformer');
    $transformer->expects($this->never())
                ->method('setTranslator');

    $this->field->setValueTransformer($transformer);
    $this->field->bind('symfony');
  }

  protected function createMockTransformer()
  {
    return $this->getMock('Symfony\Components\Form\ValueTransformer\ValueTransformerInterface', array(), array(), '', false, false);
  }

  protected function createMockTransformerTransformingTo($value)
  {
    $transformer = $this->createMockTransformer();
    $transformer->expects($this->any())
                ->method('reverseTransform')
                ->will($this->returnValue($value));

    return $transformer;
  }
}
