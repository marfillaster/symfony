<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\DateToStringTransformer;


class DateToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransformShort()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'short',
    ));
    $this->assertEquals('03.02.10', $transformer->transform('2010-02-03'));
  }

  public function testTransformMedium()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'medium',
    ));
    $this->assertEquals('03.02.2010', $transformer->transform('2010-02-03'));
  }

  public function testTransformLong()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'long',
    ));
    $this->assertEquals('03. Februar 2010', $transformer->transform('2010-02-03'));
  }

  public function testTransformFull()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'full',
    ));
    $this->assertEquals('Mittwoch, 03. Februar 2010', $transformer->transform('2010-02-03'));
  }

  public function testTransformToDifferentLocale()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $this->assertEquals('Feb 3, 2010', $transformer->transform('2010-02-03'));
  }

  public function testTransformToDifferentTimezone()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));

    $this->assertEquals($dateTime->format('d.m.Y'), $transformer->transform('2010-02-03'));
  }

  public function testTransformFromDifferentTimezone()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));

    $dateTime = new \DateTime('2010-02-03 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));

    $this->assertEquals($dateTime->format('d.m.Y'), $transformer->transform('2010-02-03'));
  }

  public function testTransformRequiresValidDate()
  {
    $transformer = new DateToStringTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('2010-01'); // missing days
  }

  public function testTransformWrapsIntlErrors()
  {
    $transformer = new DateToStringTransformer();

    // HOW TO REPRODUCE?

    //$this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    //$transformer->transform(1.5);
  }

  public function testReverseTransformShort()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'short',
    ));
    $this->assertEquals('2010-02-03', $transformer->reverseTransform('03.02.10'));
  }

  public function testReverseTransformMedium()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'medium',
    ));
    $this->assertEquals('2010-02-03', $transformer->reverseTransform('03.02.2010'));
  }

  public function testReverseTransformLong()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'long',
    ));
    $this->assertEquals('2010-02-03', $transformer->reverseTransform('03. Februar 2010'));
  }

  public function testReverseTransformFull()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'full',
    ));
    $this->assertEquals('2010-02-03', $transformer->reverseTransform('Mittwoch, 03. Februar 2010'));
  }

  public function testReverseTransformFromDifferentLocale()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $this->assertEquals('2010-02-03', $transformer->reverseTransform('Feb 3, 2010'));
  }

  public function testReverseTransformFromDifferentTimezone()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));

    $dateTime = new \DateTime('2010-02-03 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));

    $this->assertEquals($dateTime->format('Y-m-d'), $transformer->reverseTransform('03.02.2010'));
  }

  public function testReverseTransformToDifferentTimezone()
  {
    $transformer = new DateToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));
    $dateTime = new \DateTime('2010-02-03 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));
    $this->assertEquals($dateTime->format('Y-m-d'), $transformer->reverseTransform('03.02.2010'));
  }

  public function testReverseTransformRequiresString()
  {
    $transformer = new DateToStringTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform(12345);
  }

  public function testReverseTransformWrapsIntlErrors()
  {
    $transformer = new DateToStringTransformer();

    $this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    $transformer->reverseTransform('12345');
  }

  public function testValidateFormatOption()
  {
    $this->setExpectedException('\InvalidArgumentException');

    new DateToStringTransformer(array('format' => 'foobar'));
  }
}
