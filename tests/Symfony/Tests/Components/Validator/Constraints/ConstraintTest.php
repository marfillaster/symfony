<?php

namespace Symfony\Tests\Components\Validator\Constraints;

require_once __DIR__.'/../../../bootstrap.php';
require_once __DIR__.'/../ConstraintA.php';
require_once __DIR__.'/../ConstraintB.php';
require_once __DIR__.'/../ConstraintC.php';

use Symfony\Tests\Components\Validator\ConstraintA;
use Symfony\Tests\Components\Validator\ConstraintB;
use Symfony\Tests\Components\Validator\ConstraintC;

class ConstraintTest extends \PHPUnit_Framework_TestCase
{
  public function testSetProperties()
  {
    $constraint = new ConstraintA(array(
      'property1' => 'foo',
      'property2' => 'bar',
    ));

    $this->assertEquals('foo', $constraint->property1);
    $this->assertEquals('bar', $constraint->property2);
  }

  public function testSetNotExistingPropertyThrowsException()
  {
    $this->setExpectedException('Symfony\Components\Validator\Exception\InvalidAttributesException');

    new ConstraintA(array(
      'foo' => 'bar',
    ));
  }

  public function testMagicPropertiesAreNotAllowed()
  {
    $constraint = new ConstraintA();

    $this->setExpectedException('Symfony\Components\Validator\Exception\InvalidAttributesException');

    $constraint->foo = 'bar';
  }

  public function testSetDefaultProperty()
  {
    $constraint = new ConstraintA('foo');

    $this->assertEquals('foo', $constraint->property2);
  }

  public function testSetUndefinedDefaultProperty()
  {
    $this->setExpectedException('Symfony\Components\Validator\Exception\ConstraintDefinitionException');

    new ConstraintB('foo');
  }

  public function testRequiredAttributesMustBeDefined()
  {
    $this->setExpectedException('Symfony\Components\Validator\Exception\MissingAttributesException');

    new ConstraintC();
  }
}