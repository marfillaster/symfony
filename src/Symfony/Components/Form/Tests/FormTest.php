<?php

namespace Symfony\Components\Form\Tests;

require_once __DIR__ . '/TestInit.php';

use Symfony\Components\Form\Form;
use Symfony\Components\Form\FormField;
use Symfony\Components\Form\UploadedFile;


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

  public function testNoCsrfProtectionByDefault()
  {
    $form = new Form('author');

    $this->assertFalse($form->isCSRFProtected());
  }

  public function testDefaultCsrfProtectionCanBeEnabled()
  {
    Form::enableDefaultCSRFProtection();
    $form = new Form('author');

    $this->assertTrue($form->isCSRFProtected());
  }

  public function testDefaultLocaleCanBeSet()
  {
    Form::setDefaultLocale('de-DE-1996');
    $form = new Form('author');

    $field = $this->getMock(__NAMESPACE__ . '\LocalizableField', array(), array(), '', false, false);
    $field->expects($this->once())
          ->method('setLocale')
          ->with($this->equalTo('de-DE-1996'));

    $form->add($field);
  }

  public function testDefaultTranslatorCanBeSet()
  {
    $translator = $this->getMock('Symfony\Components\I18N\TranslatorInterface');
    Form::setDefaultTranslator($translator);
    $form = new Form('author');

    $field = $this->getMock(__NAMESPACE__ . '\TranslatableField', array(), array(), '', false, false);
    $field->expects($this->once())
          ->method('setTranslator')
          ->with($this->equalTo($translator));

    $form->add($field);
  }

  public function testBindConvertsUploadedFiles()
  {
    $file = new UploadedFile('/tmp/test.txt', 'test.txt', 'text/plain', 100, 0);

    $field = $this->createMockField('file');
    $field->expects($this->once())
          ->method('bind')
          ->with($this->equalTo($file));

    $form = new Form('author');
    $form->add($field);

    // test
    $form->bind(array(), array('file' => array(
      'name' => 'test.txt',
      'type' => 'text/plain',
      'tmp_name' => '/tmp/test.txt',
      'error' => 0,
      'size' => 100
    )));
  }

  public function testBindConvertsUploadedFilesWithPhpBug()
  {
    $file = new UploadedFile('/tmp/test.txt', 'test.txt', 'text/plain', 100, 0);

    $field = $this->createMockField('file');
    $field->expects($this->once())
          ->method('bind')
          ->with($this->equalTo($file));

    $form = new Form('author');
    $form->add($field);

    // test
    $form->bind(array(), array(
      'name' => array(
        'file' => 'test.txt',
      ),
      'type' => array(
        'file' => 'text/plain',
      ),
      'tmp_name' => array(
        'file' => '/tmp/test.txt',
      ),
      'error' => array(
        'file' => 0,
      ),
      'size' => array(
        'file' => 100,
      ),
    ));
  }

  public function testBindConvertsNestedUploadedFilesWithPhpBug()
  {
    $file = new UploadedFile('/tmp/test.txt', 'test.txt', 'text/plain', 100, 0);

    $field = $this->createMockField('article');
    $field->expects($this->once())
          ->method('bind')
          ->with($this->equalTo(array('file' => $file)));

    $form = new Form('author');
    $form->add($field);

    // test
    $form->bind(array(), array(
      'name' => array(
        'article' => array('file' => 'test.txt'),
      ),
      'type' => array(
        'article' => array('file' => 'text/plain'),
      ),
      'tmp_name' => array(
        'article' => array('file' => '/tmp/test.txt'),
      ),
      'error' => array(
        'article' => array('file' => 0),
      ),
      'size' => array(
        'article' => array('file' => 100),
      ),
    ));
  }

  public function testMultipartFormsWithoutParentsRequireFiles()
  {
    $form = new Form('author');
    $form->add($this->createMultipartMockField('file'));

    $this->setExpectedException('InvalidArgumentException');

    // should be given in second argument
    $form->bind(array('file' => 'test.txt'));
  }

  public function testMultipartFormsWithParentsRequireNoFiles()
  {
    $form = new Form('author');
    $form->add($this->createMultipartMockField('file'));

    $form->setParent($this->createMockField('group'));

    // files are expected to be converted by the parent
    $form->bind(array('file' => 'test.txt'));
  }

  protected function createMockField($key)
  {
    $field = $this->getMock(
      'Symfony\Components\Form\FormFieldInterface',
      array(),
      array(),
      '',
      false, // don't use constructor
      false  // don't call parent::__clone
    );

    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue($key));

    return $field;
  }

  protected function createMultipartMockField($key)
  {
    $field = $this->createMockField($key);
    $field->expects($this->any())
          ->method('isMultipart')
          ->will($this->returnValue(true));

    return $field;
  }
}
