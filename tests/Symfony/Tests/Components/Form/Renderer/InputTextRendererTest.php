<?php

namespace Symfony\Tests\Components\Form\Renderer;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/RendererTestCase.php';

use Symfony\Components\Form\Renderer\InputTextRenderer;


class InputTextRendererTest extends RendererTestCase
{
  protected $renderer;

  public function setUp()
  {
    $this->renderer = new InputTextRenderer();
  }

  public function testRenderTag()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" value="foobar" type="text" />',
      $this->renderer->render($field)
    );
  }

  public function testAddAttributes()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" class="my_class" value="foobar" type="text" />',
      $this->renderer->render($field, array('class' => 'my_class'))
    );
  }

  public function testRenderMaxLength()
  {
    $renderer = new InputTextRenderer(array('max_length' => 15));
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" value="foobar" type="text" max_length="15" />',
      $renderer->render($field)
    );
  }

  public function testEscapeValue()
  {
    $field = $this->createFieldMock('name', 'id', '<');

    $this->assertEquals(
      '<input id="id" name="name" value="&lt;" type="text" />',
      $this->renderer->render($field)
    );
  }
}
