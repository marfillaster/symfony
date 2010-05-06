<?php

namespace Symfony\Tests\Components\Form\Renderer;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/RendererTestCase.php';

use Symfony\Components\Form\Renderer\InputFileRenderer;


class InputFileRendererTest extends RendererTestCase
{
  protected $renderer;

  public function setUp()
  {
    $this->renderer = new InputFileRenderer();
  }

  public function testRenderTag()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" type="file" />',
      $this->renderer->render($field)
    );
  }

  public function testAddAttributes()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<input id="id" name="name" class="my_class" type="file" />',
      $this->renderer->render($field, array('class' => 'my_class'))
    );
  }
}
