<?php

namespace Symfony\Tests\Components\Form\Renderer;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/RendererTestCase.php';

use Symfony\Components\Form\Renderer\BaseInputSwitchRenderer;
use Symfony\Components\Form\FieldInterface;


class InputSwitchRenderer extends BaseInputSwitchRenderer
{
  public function render(FieldInterface $field, array $attributes = array())
  {
    $attributes['type'] = 'checkbox';

    return parent::render($field, $attributes);
  }
}

class InputSwitchRendererTest extends RendererTestCase
{
  protected $renderer;

  public function setUp()
  {
    $this->renderer = new InputSwitchRenderer();
  }

  public function testCheckedIf1()
  {
    $field = $this->createFieldMock('name', 'id', 1);

    $this->assertEquals(
      '<input id="id" name="name" type="checkbox" checked="checked" />',
      $this->renderer->render($field)
    );
  }

  public function testCheckedIf0String()
  {
    $field = $this->createFieldMock('name', 'id', '0');

    $this->assertEquals(
      '<input id="id" name="name" type="checkbox" checked="checked" />',
      $this->renderer->render($field)
    );
  }

  public function testUnheckedIfNull()
  {
    $field = $this->createFieldMock('name', 'id', null);

    $this->assertEquals('<input id="id" name="name" type="checkbox" />',
      $this->renderer->render($field)
    );
  }

  public function testUnheckedIfFalse()
  {
    $field = $this->createFieldMock('name', 'id', false);

    $this->assertEquals(
      '<input id="id" name="name" type="checkbox" />',
      $this->renderer->render($field)
    );
  }

  public function testUnheckedIfEmptyString()
  {
    $field = $this->createFieldMock('name', 'id', '');

    $this->assertEquals(
      '<input id="id" name="name" type="checkbox" />',
      $this->renderer->render($field)
    );
  }

  public function testUnheckedIf0()
  {
    $field = $this->createFieldMock('name', 'id', 0);

    $this->assertEquals(
      '<input id="id" name="name" type="checkbox" />',
      $this->renderer->render($field)
    );
  }

  public function testSetValue()
  {
    $renderer = new InputSwitchRenderer(array('value' => 'foobar'));
    $field = $this->createFieldMock('name', 'id', 1);

    $this->assertEquals(
      '<input id="id" name="name" type="checkbox" value="foobar" checked="checked" />',
      $renderer->render($field)
    );
  }

  public function testEscapeValue()
  {
    $renderer = new InputSwitchRenderer(array('value' => '<'));
    $field = $this->createFieldMock('name', 'id', 1);

    $this->assertEquals(
      '<input id="id" name="name" type="checkbox" value="&lt;" checked="checked" />',
      $renderer->render($field)
    );
  }
}
