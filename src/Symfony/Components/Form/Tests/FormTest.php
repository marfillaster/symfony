<?php

namespace Symfony\Components\Form\Tests;

require_once __DIR__ . '/TestInit.php';

use Symfony\Components\Form\Form;
use Symfony\Components\Form\Field;
use Symfony\Components\Form\FieldGroup;
use Symfony\Components\File\UploadedFile;
use Symfony\Components\Validator\Engine\PropertyPathBuilder;
use Symfony\Components\Validator\Engine\ConstraintViolation;
use Symfony\Components\Validator\Engine\ConstraintViolationList;


class FormTest_Object
{
  public $firstName;
  public $file;
  public $child;
}

class FormTest_PreconfiguredForm extends Form
{
  protected function configure()
  {
    $this->add(new Field('firstName'));
  }
}

class FormTest extends \PHPUnit_Framework_TestCase
{
  protected $validator;
  protected $object;
  protected $form;

  protected function setUp()
  {
    Form::disableDefaultCSRFProtection();
    $this->validator = $this->createMockValidator();
    $this->object = new FormTest_Object();
    $this->object->child = new FormTest_Object();
    $this->form = new Form('author', $this->object, $this->validator);
  }

  public function testConstructInitializesObject()
  {
    $this->assertEquals($this->object, $this->form->getData());
  }

  public function testNoCsrfProtectionByDefault()
  {
    $form = new Form('author', $this->object, $this->validator);

    $this->assertFalse($form->isCSRFProtected());
  }

  public function testDefaultCsrfProtectionCanBeEnabled()
  {
    Form::enableDefaultCSRFProtection();
    $form = new Form('author', $this->object, $this->validator);

    $this->assertTrue($form->isCSRFProtected());
  }

  public function testDefaultLocaleCanBeSet()
  {
    Form::setDefaultLocale('de-DE-1996');
    $form = new Form('author', $this->object, $this->validator);

    $field = $this->getMock(__NAMESPACE__ . '\LocalizableField', array(), array(), '', false, false);
    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue('firstName'));
    $field->expects($this->once())
          ->method('setLocale')
          ->with($this->equalTo('de-DE-1996'));

    $form->add($field);
  }

  public function testDefaultTranslatorCanBeSet()
  {
    $translator = $this->getMock('Symfony\Components\I18N\TranslatorInterface');
    Form::setDefaultTranslator($translator);
    $form = new Form('author', $this->object, $this->validator);

    $field = $this->getMock(__NAMESPACE__ . '\TranslatableField', array(), array(), '', false, false);
    $field->expects($this->any())
          ->method('getKey')
          ->will($this->returnValue('firstName'));
    $field->expects($this->once())
          ->method('setTranslator')
          ->with($this->equalTo($translator));

    $form->add($field);
  }

  public function testBindConvertsUploadedFiles()
  {
    $tmpFile = $this->createTempFile();
    $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain', 100, 0);

    $field = $this->createMockField('file');
    $field->expects($this->once())
          ->method('bind')
          ->with($this->equalTo($file));

    $form = new Form('author', $this->object, $this->validator);
    $form->add($field);

    // test
    $form->bind(array(), array('file' => array(
      'name' => basename($tmpFile),
      'type' => 'text/plain',
      'tmp_name' => $tmpFile,
      'error' => 0,
      'size' => 100
    )));
  }

  public function testBindConvertsUploadedFilesWithPhpBug()
  {
    $tmpFile = $this->createTempFile();
    $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain', 100, 0);

    $field = $this->createMockField('file');
    $field->expects($this->once())
          ->method('bind')
          ->with($this->equalTo($file));

    $form = new Form('author', $this->object, $this->validator);
    $form->add($field);

    // test
    $form->bind(array(), array(
      'name' => array(
        'file' => basename($tmpFile),
      ),
      'type' => array(
        'file' => 'text/plain',
      ),
      'tmp_name' => array(
        'file' => $tmpFile,
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
    $tmpFile = $this->createTempFile();
    $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain', 100, 0);

    $group = $this->createMockFieldGroup('article');
    $group->expects($this->once())
          ->method('bind')
          ->with($this->equalTo(array('file' => $file)));

    $form = new Form('author', $this->object, $this->validator);
    $form->merge($group);

    // test
    $form->bind(array(), array(
      'name' => array(
        'article' => array('file' => basename($tmpFile)),
      ),
      'type' => array(
        'article' => array('file' => 'text/plain'),
      ),
      'tmp_name' => array(
        'article' => array('file' => $tmpFile),
      ),
      'error' => array(
        'article' => array('file' => 0),
      ),
      'size' => array(
        'article' => array('file' => 100),
      ),
    ));
  }

  public function testBindMapsFieldValidationErrorsOntoFields()
  {
    $builder = new PropertyPathBuilder();
    $violations = new ConstraintViolationList();
    $violations->add(new ConstraintViolation(
      'message',
      array('param' => 'value'),
      'Form',
      $builder->atProperty('fields')->atIndex('firstName')
              ->atProperty('displayedData')
              ->getPropertyPath(),
      'invalid value'
    ));

    $validator = $this->createMockValidator();
    $field = $this->createMockField('firstName');
    $form = new Form('author', $this->object, $validator);
    $form->add($field);

    $validator->expects($this->once())
              ->method('validate')
              ->with($this->equalTo($form))
              ->will($this->returnValue($violations));

    $field->expects($this->once())
          ->method('addError')
          ->with($this->equalTo('message'), $this->equalTo(array('param' => 'value')));

    $form->bind(array()); // irrelevant
  }

  public function testBindMapsFieldValidationErrorsOntoNestedFields()
  {
    $builder = new PropertyPathBuilder();
    $violations = new ConstraintViolationList();
    $violations->add(new ConstraintViolation(
      'message',
      array('param' => 'value'),
      'Form',
      $builder->atProperty('fields')->atIndex('child')
              ->atProperty('fields')->atIndex('firstName')
              ->atProperty('displayedData')
              ->getPropertyPath(),
      'invalid value'
    ));

    $validator = $this->createMockValidator();
    $field = $this->createMockField('firstName');
    $form = new Form('author', $this->object, $validator);
    $group = new FieldGroup('child');
    $group->add($field);
    $form->add($group);

    $validator->expects($this->once())
              ->method('validate')
              ->with($this->equalTo($form))
              ->will($this->returnValue($violations));

    $field->expects($this->once())
          ->method('addError')
          ->with($this->equalTo('message'), $this->equalTo(array('param' => 'value')));

    $form->bind(array()); // irrelevant
  }

  public function testBindMapsModelValidationErrorsOntoFields()
  {
    $builder = new PropertyPathBuilder();
    $violations = new ConstraintViolationList();
    $violations->add(new ConstraintViolation(
      'message',
      array('param' => 'value'),
      'Form',
      $builder->atProperty('data')
              ->atProperty('firstName')
              ->getPropertyPath(),
      'invalid value'
    ));

    $validator = $this->createMockValidator();
    $field = $this->createMockField('firstName');
    $form = new Form('author', $this->object, $validator);
    $form->add($field);

    $validator->expects($this->once())
              ->method('validate')
              ->with($this->equalTo($form))
              ->will($this->returnValue($violations));

    $field->expects($this->once())
          ->method('addError')
          ->with($this->equalTo('message'), $this->equalTo(array('param' => 'value')));

    $form->bind(array()); // irrelevant
  }

  public function testBindMapsModelValidationErrorsOntoNestedFields()
  {
    $builder = new PropertyPathBuilder();
    $violations = new ConstraintViolationList();
    $violations->add(new ConstraintViolation(
      'message',
      array('param' => 'value'),
      'Form',
      $builder->atProperty('data')
              ->atProperty('child')
              ->atProperty('firstName')
              ->getPropertyPath(),
      'invalid value'
    ));

    $validator = $this->createMockValidator();
    $field = $this->createMockField('firstName');
    $form = new Form('author', $this->object, $validator);
    $group = new FieldGroup('child');
    $group->add($field);
    $form->add($group);

    $validator->expects($this->once())
              ->method('validate')
              ->with($this->equalTo($form))
              ->will($this->returnValue($violations));

    $field->expects($this->once())
          ->method('addError')
          ->with($this->equalTo('message'), $this->equalTo(array('param' => 'value')));

    $form->bind(array()); // irrelevant
  }

  public function testBindMapsModelValidationErrorsOntoMergedFields()
  {
    $builder = new PropertyPathBuilder();
    $violations = new ConstraintViolationList();
    $violations->add(new ConstraintViolation(
      'message',
      array('param' => 'value'),
      'Form',
      $builder->atProperty('data')
              ->atProperty('firstName')
              ->getPropertyPath(),
      'invalid value'
    ));

    $validator = $this->createMockValidator();
    $field = $this->createMockField('firstName');
    $form = new Form('author', $this->object, $validator);
    $group = new FieldGroup('group');
    $group->add($field);
    $form->merge($group);

    $validator->expects($this->once())
              ->method('validate')
              ->with($this->equalTo($form))
              ->will($this->returnValue($violations));

    $field->expects($this->once())
          ->method('addError')
          ->with($this->equalTo('message'), $this->equalTo(array('param' => 'value')));

    $form->bind(array()); // irrelevant
  }

  public function testMultipartFormsWithoutParentsRequireFiles()
  {
    $form = new Form('author', $this->object, $this->validator);
    $form->add($this->createMultipartMockField('file'));

    $this->setExpectedException('InvalidArgumentException');

    // should be given in second argument
    $form->bind(array('file' => 'test.txt'));
  }

  public function testMultipartFormsWithParentsRequireNoFiles()
  {
    $form = new Form('author', $this->object, $this->validator);
    $form->add($this->createMultipartMockField('file'));

    $form->setParent($this->createMockField('group'));

    // files are expected to be converted by the parent
    $form->bind(array('file' => 'test.txt'));
  }

  protected function createMockField($key)
  {
    $field = $this->getMock(
      'Symfony\Components\Form\FieldInterface',
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

  protected function createMockFieldGroup($key)
  {
    $field = $this->getMock(
      'Symfony\Components\Form\FieldGroup',
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

  protected function createTempFile()
  {
    return tempnam(sys_get_temp_dir(), 'FormTest');
  }

  protected function createMockValidator()
  {
    return $this->getMock('Symfony\Components\Validator\ValidatorInterface');
  }
}
