<?php

namespace Symfony\Tests\Components\Form\Renderer;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/RendererTestCase.php';

use Symfony\Components\Form\Renderer\InputHiddenRenderer;


class InputHiddenRendererTest extends RendererTestCase
{
  protected $renderer;

  public function setUp()
  {
    $this->renderer = new InputHiddenRenderer();
  }

  public function testRenderTag()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" type="hidden" value="foobar" />',
      $this->renderer->render($field)
    );
  }

  public function testAddAttributes()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" class="my_class" type="hidden" value="foobar" />',
      $this->renderer->render($field, array('class' => 'my_class'))
    );
  }

  public function testEscapeValue()
  {
    $field = $this->createFieldMock('name', 'id', '<');

    $this->assertEquals(
      '<input id="id" name="name" type="hidden" value="&lt;" />',
      $this->renderer->render($field)
    );
  }
}
