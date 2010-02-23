<?php

namespace Symfony\Components\Form\Tests;

require_once __DIR__ . '/TestInit.php';

use Symfony\Components\Form\BaseFormField;


abstract class BaseFormFieldTest_InvalidFormField extends BaseFormField
{
  public function isValid()
  {
    return false;
  }
}


class BaseFormFieldTest extends \PHPUnit_Framework_TestCase
{
  protected $field;

  protected function setUp()
  {
    $this->field = $this->createMockBaseField('title');
    $this->field->setRenderer($this->createMockRenderer());
  }

  public function testUnboundFieldIsInvalid()
  {
    $this->assertFalse($this->field->isValid());
  }

  public function testGetNameReturnsKey()
  {
    $this->assertEquals('title', $this->field->getName());
  }

  public function testGetNameIncludesParent()
  {
    $this->field->setParent($this->createMockGroupWithName('news[article]'));

    $this->assertEquals('news[article][title]', $this->field->getName());
  }

  public function testGetIdReturnsKey()
  {
    $this->assertEquals('title', $this->field->getId());
  }

  public function testGetIdIncludesParent()
  {
    $this->field->setParent($this->createMockGroupWithId('news_article'));

    $this->assertEquals('news_article_title', $this->field->getId());
  }

  public function testRenderForwardsToRenderer()
  {
    $renderer = $this->createMockRenderer();
    $renderer->expects($this->once())
             ->method('render')
             ->with($this->equalTo($this->field), $this->equalTo(array('foo' => 'bar')))
             ->will($this->returnValue('HTML'));

    $this->field->setRenderer($renderer);

    // test
    $output = $this->field->render(array('foo' => 'bar'));

    $this->assertEquals('HTML', $output);
  }

  public function testRenderThrowsExceptionIfNoRendererIsSet()
  {
    $field = $this->createMockBaseField('name');

    $this->setExpectedException('Symfony\Components\Form\Exception\InvalidConfigurationException');
    $field->render();
  }

  public function testProcessThrowsExceptionIfNotBound()
  {
    $this->setExpectedException('Symfony\Components\Form\Exception\NotBoundException');
    $this->field->process();
  }

  public function testProcessThrowsExceptionIfInvalid()
  {
    $field = $this->getMockForAbstractClass(
      __NAMESPACE__ . '\BaseFormFieldTest_InvalidFormField',
      array('title')
    );

    $field->bind('foobar');

    $this->setExpectedException('Symfony\Components\Form\Exception\NotValidException');
    $field->process();
  }

  public function testLocaleIsPassedToLocalizableRenderer()
  {
    $renderer = $this->getMock(__NAMESPACE__ . '\LocalizableRenderer');
    $renderer->expects($this->once())
             ->method('setLocale')
             ->with($this->equalTo('de_DE'));

    $this->field->setRenderer($renderer);
    $this->field->setLocale('de_DE');
    $this->field->render();
  }

  public function testTranslatorIsPassedToTranslatableRenderer()
  {
    $translator = $this->getMock('Symfony\Components\I18N\TranslatorInterface');
    $renderer = $this->getMock(__NAMESPACE__ . '\TranslatableRenderer');
    $renderer->expects($this->once())
             ->method('setTranslator')
             ->with($this->equalTo($translator));

    $this->field->setRenderer($renderer);
    $this->field->setTranslator($translator);
    $this->field->render();
  }

  public function testTranslatorIsNotPassedToRendererIfNotSet()
  {
    $renderer = $this->getMock(__NAMESPACE__ . '\TranslatableRenderer');
    $renderer->expects($this->never())
             ->method('setTranslator');

    $this->field->setRenderer($renderer);
    $this->field->render();
  }

  public function testIsRequiredReturnsOwnValueIfParentIsRequired()
  {
    $group = $this->createMockGroup();
    $group->expects($this->any())
          ->method('isRequired')
          ->will($this->returnValue(true));

    $this->field->setParent($group);

    $this->field->setRequired(true);
    $this->assertTrue($this->field->isRequired());

    $this->field->setRequired(false);
    $this->assertFalse($this->field->isRequired());
  }

  public function testIsRequiredReturnsFalseIfParentIsNotRequired()
  {
    $group = $this->createMockGroup();
    $group->expects($this->any())
          ->method('isRequired')
          ->will($this->returnValue(false));

    $this->field->setParent($group);
    $this->field->setRequired(true);

    $this->assertFalse($this->field->isRequired());
  }

  protected function createMockBaseField($key)
  {
    return $this->getMockForAbstractClass(
      'Symfony\Components\Form\BaseFormField',
      array($key)
    );
  }

  protected function createMockGroup()
  {
    return $this->getMock(
      'Symfony\Components\Form\FormFieldGroup',
      array(),
      array(),
      '',
      false // don't call constructor
    );
  }

  protected function createMockGroupWithName($name)
  {
    $group = $this->createMockGroup();
    $group->expects($this->any())
                ->method('getName')
                ->will($this->returnValue($name));

    return $group;
  }

  protected function createMockGroupWithId($id)
  {
    $group = $this->createMockGroup();
    $group->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($id));

    return $group;
  }

  protected function createMockRenderer()
  {
    return $this->getMock('Symfony\Components\Form\Renderer\RendererInterface');
  }
}
