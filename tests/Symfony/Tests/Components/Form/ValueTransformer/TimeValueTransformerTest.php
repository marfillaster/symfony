<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\TimeValueTransformer;


class TimeValueTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransformTimeToShortTimeString()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'short',
    ));
    $this->assertEquals('04:05', $transformer->transform('04:05:06'));
  }

  public function testTransformTimeToMediumTimeString()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'medium',
    ));
    $this->assertEquals('04:05:06', $transformer->transform('04:05:06'));
  }

  public function testTransformTimeToLongTimeString()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'long',
    ));
    $this->assertEquals('04:05:06 GMT+00:00', $transformer->transform('04:05:06'));
  }

  public function testTransformTimeToFullTimeString()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'full',
    ));
    $this->assertEquals('04:05:06 GMT+00:00', $transformer->transform('04:05:06'));
  }

  public function testTransformTimestampToTimeString()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'input' => 'timestamp',
    ));
    $dateTime = new \DateTime('04:05 UTC');
    $this->assertEquals('04:05', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformTimeToArray()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'output' => 'array',
    ));
    $output = array(
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );
    $this->assertEquals($output, $transformer->transform('04:05:06'));
  }

  public function testTransformToDifferentLocale()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $this->assertEquals('4:05 AM', $transformer->transform('04:05:06'));
  }

  public function testTransformToDifferentTimezone()
  {
    $transformer = new TimeValueTransformer(array(
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
    $transformer = new TimeValueTransformer(array(
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
    $transformer = new TimeValueTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('2010-01-01 01:01'); // missing seconds
  }

  public function testTransformRequiresValidTimestamp()
  {
    $transformer = new TimeValueTransformer(array('input' => 'timestamp'));

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('foobar'); // no integer
  }

  public function testTransformWrapsIntlErrors()
  {
    $transformer = new TimeValueTransformer(array('input' => 'timestamp'));

    // HOW TO REPRODUCE?

    //$this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    //$transformer->transform(1.5);
  }

  public function testReverseTransformShortTimeStringToTime()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'short',
    ));
    $this->assertEquals('04:05:00', $transformer->reverseTransform('04:05'));
  }

  public function testReverseTransformMediumTimeStringToTime()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'medium',
    ));
    $this->assertEquals('04:05:06', $transformer->reverseTransform('04:05:06'));
  }

  public function testReverseTransformLongTimeStringToTime()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'long',
    ));
    $this->assertEquals('04:05:06', $transformer->reverseTransform('04:05:06 GMT+00:00'));
  }

  public function testReverseTransformFullTimeStringToTime()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'format' => 'full',
    ));
    $this->assertEquals('04:05:06', $transformer->reverseTransform('04:05:06 GMT+00:00'));
  }

  public function testReverseTransformToTimestamp()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'input' => 'timestamp',
    ));
    $this->assertEquals(((4*60)+5)*60, $transformer->reverseTransform('04:05'));
  }

  public function testReverseTransformArrayToTime()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'output' => 'array',
    ));
    $input = array(
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );
    $this->assertEquals('04:05:06', $transformer->reverseTransform($input));
  }

  public function testReverseTransformFromDifferentLocale()
  {
    $transformer = new TimeValueTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $this->assertEquals('04:05:00', $transformer->reverseTransform('4:05 AM'));
  }

  public function testReverseTransformFromDifferentTimezone()
  {
    $transformer = new TimeValueTransformer(array(
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
    $transformer = new TimeValueTransformer(array(
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
    $transformer = new TimeValueTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform(12345);
  }

  public function testReverseTransformRequiresArray()
  {
    $transformer = new TimeValueTransformer(array('output' => 'array'));

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform('12345');
  }

  public function testReverseTransformWrapsIntlErrors()
  {
    $transformer = new TimeValueTransformer();

    $this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    $transformer->reverseTransform('12345');
  }

  public function testValidateFormatOption()
  {
    $this->setExpectedException('\InvalidArgumentException');

    new TimeValueTransformer(array('format' => 'foobar'));
  }
}
