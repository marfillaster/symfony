<?php

namespace Symfony\Tests\Components\Form\Renderer;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/RendererTestCase.php';

use Symfony\Components\Form\Renderer\TextareaRenderer;


class TextareaRendererTest extends RendererTestCase
{
  protected $renderer;

  public function setUp()
  {
    $this->renderer = new TextareaRenderer();
  }

  public function testRenderTag()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<textarea rows="4" cols="30" id="id" name="name">foobar</textarea>',
      $this->renderer->render($field)
    );
  }

  public function testAddAttributes()
  {
    $field = $this->createFieldMock('name', 'id', 'foobar');

    $this->assertEquals(
      '<textarea rows="4" cols="30" class="my_class" id="id" name="name">foobar</textarea>',
      $this->renderer->render($field, array('class' => 'my_class'))
    );
  }

  public function testEscapeValue()
  {
    $field = $this->createFieldMock('name', 'id', '<');

    $this->assertEquals(
      '<textarea rows="4" cols="30" id="id" name="name">&lt;</textarea>',
      $this->renderer->render($field)
    );
  }
}
