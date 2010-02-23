<?php

namespace Symfony\Components\Form\Tests;

require_once __DIR__ . '/TestInit.php';

use Symfony\Components\Form\Form;
use Symfony\Components\Form\FormField;


class FormTest_PreconfiguredForm extends Form
{
  protected function configure()
  {
    $this->add(new FormField('first_name'));
  }
}

class FormTest extends \PHPUnit_Framework_TestCase
{
  protected $form;

  protected function setUp()
  {
    Form::disableDefaultCSRFProtection();
    $this->form = new Form('author');
  }

  public function testConstructAcceptsDefaultValues()
  {
    $form = new FormTest_PreconfiguredForm('author', array('first_name' => 'Fabien'));

    $this->assertEquals('Fabien', $form->get('first_name')->getData());
  }

  protected function createMockField($key)
  {
    $field = $this->getMock('Symfony\Components\Form\FormFieldInterface', array(), array(), '', false, false);
    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue($key));

    return $field;
  }
}
