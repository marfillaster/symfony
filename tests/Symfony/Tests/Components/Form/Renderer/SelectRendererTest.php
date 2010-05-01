<?php

namespace Symfony\Tests\Components\Form\Renderer;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/RendererTestCase.php';

use Symfony\Components\Form\Renderer\SelectRenderer;


class SelectRendererTest extends RendererTestCase
{
  protected $renderer;

  public function setUp()
  {
    $this->renderer = new SelectRenderer();
  }

  public function testRenderTag()
  {
    $field = $this->createChoiceFieldMock('name', 'id', 'foo', array(
      'foo' => 'bar',
      'moo' => 'maa',
    ));

    $this->assertEquals(
<<<EOF
<select id="id" name="name">
<option value="foo" selected="selected">bar</option>
<option value="moo">maa</option>
</select>
EOF
      , $this->renderer->render($field)
    );
  }

  public function testAddAttributes()
  {
    $field = $this->createChoiceFieldMock('name', 'id', 'foo', array(
      'foo' => 'bar',
      'moo' => 'maa',
    ));

    $this->assertEquals(
<<<EOF
<select class="my_class" id="id" name="name">
<option value="foo" selected="selected">bar</option>
<option value="moo">maa</option>
</select>
EOF
      , $this->renderer->render($field, array('class' => 'my_class'))
    );
  }

  public function testRenderOptGroups()
  {
    $field = $this->createChoiceFieldMock('name', 'id', 'fi', array(
      'first' => array(
        'foo' => 'bar',
        'moo' => 'maa',
      ),
      'second' => array(
        'fi' => 'fo',
        'mee' => 'mo',
      )
    ));

    $this->assertEquals(
<<<EOF
<select id="id" name="name">
<optgroup label="first">
<option value="foo">bar</option>
<option value="moo">maa</option>
</optgroup>
<optgroup label="second">
<option value="fi" selected="selected">fo</option>
<option value="mee">mo</option>
</optgroup>
</select>
EOF
      , $this->renderer->render($field)
    );
  }

  public function testSetEmptyOptionValue()
  {
    $field = $this->createChoiceFieldMock('name', 'id', '', array(
      '' => 'foobar',
    ));

    $this->assertEquals(
<<<EOF
<select id="id" name="name">
<option value="" selected="selected">foobar</option>
</select>
EOF
      , $this->renderer->render($field)
    );
  }

  public function testSelectMultiple()
  {
    $field = $this->createChoiceFieldMock('name', 'id', array('foo', 'moo'), array(
      'foo' => 'bar',
      'moo' => 'maa',
    ));

    $this->assertEquals(
<<<EOF
<select id="id" name="name">
<option value="foo" selected="selected">bar</option>
<option value="moo" selected="selected">maa</option>
</select>
EOF
      , $this->renderer->render($field)
    );
  }

  public function testEscapeKeysAndValues()
  {
    $field = $this->createChoiceFieldMock('name', 'id', null, array(
      '<>' => array('<' => '>'),
    ));

    $this->assertEquals(
<<<EOF
<select id="id" name="name">
<optgroup label="&lt;&gt;">
<option value="&lt;">&gt;</option>
</optgroup>
</select>
EOF
      , $this->renderer->render($field)
    );
  }

  protected function createChoiceFieldMock($name, $id, $displayedData, $choices)
  {
    $field = $this->getMock('Symfony\Components\Form\ChoiceFieldInterface');

    $field->expects($this->any())
          ->method('getDisplayedData')
          ->will($this->returnValue($displayedData));
    $field->expects($this->any())
          ->method('getName')
          ->will($this->returnValue($name));
    $field->expects($this->any())
          ->method('getId')
          ->will($this->returnValue($id));
    $field->expects($this->any())
          ->method('getChoices')
          ->will($this->returnValue($choices));

    return $field;
  }
}
