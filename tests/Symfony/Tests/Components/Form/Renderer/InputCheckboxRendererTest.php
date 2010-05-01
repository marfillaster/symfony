<?php

namespace Symfony\Tests\Components\Form\Renderer;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/RendererTestCase.php';

use Symfony\Components\Form\Renderer\InputCheckboxRenderer;


class InputCheckboxRendererTest extends RendererTestCase
{
  protected $renderer;

  public function setUp()
  {
    $this->renderer = new InputCheckboxRenderer();
  }

  public function testRenderTag()
  {
    $field = $this->createFieldMock('name', 'id', 1);

    $this->assertEquals(
      '<input id="id" name="name" type="checkbox" checked="checked" />',
      $this->renderer->render($field)
    );
  }

  public function testAddAttributes()
  {
    $field = $this->createFieldMock('name', 'id', 0);

    $this->assertEquals(
      '<input id="id" name="name" class="my_class" type="checkbox" />',
      $this->renderer->render($field, array('class' => 'my_class'))
    );
  }
}
