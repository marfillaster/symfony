<?php

namespace Symfony\Tests\Components\Form\Renderer;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/RendererTestCase.php';

use Symfony\Components\Form\Renderer\InputPasswordRenderer;


class InputPasswordRendererTest extends RendererTestCase
{
  protected $renderer;

  public function setUp()
  {
    $this->renderer = new InputPasswordRenderer();
  }

  public function testRenderTag()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" type="password" />',
      $this->renderer->render($field)
    );
  }

  public function testRenderWithValue()
  {
    $renderer = new InputPasswordRenderer(array('always_empty' => false));
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" type="password" value="foobar" />',
      $renderer->render($field)
    );
  }

  public function testEscapeValue()
  {
    $renderer = new InputPasswordRenderer(array('always_empty' => false));
    $field = $this->createFieldMock('name', 'id', '<');

    $this->assertEquals(
      '<input id="id" name="name" type="password" value="&lt;" />',
      $renderer->render($field)
    );
  }

  public function testAddAttributes()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" class="my_class" type="password" />',
      $this->renderer->render($field, array('class' => 'my_class'))
    );
  }
}
