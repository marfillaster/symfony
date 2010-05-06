<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\TimeToStringTransformer;


class TimeToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransformShort()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'short',
    ));
    $this->assertEquals('04:05', $transformer->transform('04:05:06'));
  }

  public function testTransformMedium()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'medium',
    ));
    $this->assertEquals('04:05:06', $transformer->transform('04:05:06'));
  }

  public function testTransformLong()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'long',
    ));
    $this->assertEquals('04:05:06 GMT+00:00', $transformer->transform('04:05:06'));
  }

  public function testTransformFull()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'full',
    ));
    $this->assertEquals('04:05:06 GMT+00:00', $transformer->transform('04:05:06'));
  }

  public function testTransformToDifferentLocale()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $this->assertEquals('4:05 AM', $transformer->transform('04:05:06'));
  }

  public function testTransformToDifferentTimezone()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));
    $dateTime = new \DateTime('04:05:06 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));
    $this->assertEquals($dateTime->format('H:i'), $transformer->transform('04:05:06'));
  }

  public function testTransformFromDifferentTimezone()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));
    $dateTime = new \DateTime('06:05:06 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));
    $this->assertEquals($dateTime->format('H:i'), $transformer->transform('06:05:06'));
  }

  public function testTransformRequiresValidTime()
  {
    $transformer = new TimeToStringTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('2010-01-01 01:01'); // missing seconds
  }

  public function testTransformWrapsIntlErrors()
  {
    $transformer = new TimeToStringTransformer();

    // HOW TO REPRODUCE?

    //$this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    //$transformer->transform(1.5);
  }

  public function testReverseTransformShort()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'short',
    ));
    $this->assertEquals('04:05:00', $transformer->reverseTransform('04:05'));
  }

  public function testReverseTransformMedium()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'medium',
    ));
    $this->assertEquals('04:05:06', $transformer->reverseTransform('04:05:06'));
  }

  public function testReverseTransformLong()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'long',
    ));
    $this->assertEquals('04:05:06', $transformer->reverseTransform('04:05:06 GMT+00:00'));
  }

  public function testReverseTransformFull()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'full',
    ));
    $this->assertEquals('04:05:06', $transformer->reverseTransform('04:05:06 GMT+00:00'));
  }

  public function testReverseTransformFromDifferentLocale()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $this->assertEquals('04:05:00', $transformer->reverseTransform('4:05 AM'));
  }

  public function testReverseTransformFromDifferentTimezone()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));
    $dateTime = new \DateTime('1970-01-01 04:05 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));
    $this->assertEquals($dateTime->format('H:i:s'), $transformer->reverseTransform('04:05'));
  }

  public function testReverseTransformToDifferentTimezone()
  {
    $transformer = new TimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));
    $dateTime = new \DateTime('1970-01-01 04:05 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));
    $this->assertEquals($dateTime->format('H:i:s'), $transformer->reverseTransform('04:05'));
  }

  public function testReverseTransformRequiresString()
  {
    $transformer = new TimeToStringTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform(12345);
  }

  public function testReverseTransformWrapsIntlErrors()
  {
    $transformer = new TimeToStringTransformer();

    $this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    $transformer->reverseTransform('12345');
  }

  public function testValidateFormatOption()
  {
    $this->setExpectedException('\InvalidArgumentException');

    new TimeToStringTransformer(array('format' => 'foobar'));
  }
}
