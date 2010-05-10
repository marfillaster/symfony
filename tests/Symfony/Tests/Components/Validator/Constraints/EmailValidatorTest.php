<?php

namespace Symfony\Tests\Components\Validator;

require_once __DIR__.'/../../../bootstrap.php';

use Symfony\Components\Validator\Constraints\Email;
use Symfony\Components\Validator\Constraints\EmailValidator;

class EmailValidatorTest extends \PHPUnit_Framework_TestCase
{
  protected $validator;

  public function setUp()
  {
    $this->validator = new EmailValidator();
  }

  /**
   * @dataProvider getValidEmails
   */
  public function testValidEmails($email)
  {
    $this->assertTrue($this->validator->isValid($email, new Email()));
  }

  public function getValidEmails()
  {
    return array(
      array('fabien.potencier@symfony-project.com'),
      array('example@example.co.uk'),
      array('fabien_potencier@example.fr'),
    );
  }

  /**
   * @dataProvider getInvalidEmails
   */
  public function testInvalidEmails($email)
  {
    $this->assertFalse($this->validator->isValid($email, new Email()));
  }

  public function getInvalidEmails()
  {
    return array(
      array('example'),
      array('example@'),
      array('example@localhost'),
      array('example@example.com@example.com'),
    );
  }

  public function testMessageIsSet()
  {
    $constraint = new Email(array(
      'message' => 'myMessage'
    ));

    $this->assertFalse($this->validator->isValid('foobar', $constraint));
    $this->assertEquals($this->validator->getMessageTemplate(), 'myMessage');
    $this->assertEquals($this->validator->getMessageParameters(), array(
      'value' => 'foobar',
    ));
  }
}