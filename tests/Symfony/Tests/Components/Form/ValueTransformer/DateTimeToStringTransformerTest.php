<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\DateTimeToStringTransformer;


class DateTimeToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransformShortDate()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'short',
    ));
    $this->assertEquals('03.02.10 04:05', $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformMediumDate()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'medium',
    ));
    $this->assertEquals('03.02.2010 04:05', $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformLongDate()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'long',
    ));
    $this->assertEquals('03. Februar 2010 04:05', $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformFullDate()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'full',
    ));
    $this->assertEquals('Mittwoch, 03. Februar 2010 04:05', $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformShortTime()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'time_format' => 'short',
    ));
    $this->assertEquals('03.02.2010 04:05', $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformMediumTime()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'time_format' => 'medium',
    ));
    $this->assertEquals('03.02.2010 04:05:06', $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformLongTime()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'time_format' => 'long',
    ));
    $this->assertEquals('03.02.2010 04:05:06 GMT+00:00', $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformFullTime()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'time_format' => 'full',
    ));
    $this->assertEquals('03.02.2010 04:05:06 GMT+00:00', $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformToDifferentLocale()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $this->assertEquals('Feb 3, 2010 4:05 AM', $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformToDifferentTimezone()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05:06 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));

    $this->assertEquals($dateTime->format('d.m.Y H:i'), $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformFromDifferentTimezone()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05:06 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));

    $this->assertEquals($dateTime->format('d.m.Y H:i'), $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformRequiresValidDate()
  {
    $transformer = new DateTimeToStringTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('2010-01-01 01'); // missing minutes
  }

  public function testTransformWrapsIntlErrors()
  {
    $transformer = new DateTimeToStringTransformer();

    // HOW TO REPRODUCE?

    //$this->setExpectedException('Symfony\Components\Form\ValueTransformer\Transdate_formationFailedException');

    //$transformer->transform(1.5);
  }

  public function testReverseTransformShortDate()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'short',
    ));
    $this->assertEquals('2010-02-03 04:05:00', $transformer->reverseTransform('03.02.10 04:05'));
  }

  public function testReverseTransformMediumDate()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'medium',
    ));
    $this->assertEquals('2010-02-03 04:05:00', $transformer->reverseTransform('03.02.2010 04:05'));
  }

  public function testReverseTransformLongDate()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'long',
    ));
    $this->assertEquals('2010-02-03 04:05:00', $transformer->reverseTransform('03. Februar 2010 04:05'));
  }

  public function testReverseTransformFullDate()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'full',
    ));
    $this->assertEquals('2010-02-03 04:05:00', $transformer->reverseTransform('Mittwoch, 03. Februar 2010 04:05'));
  }

  public function testReverseTransformShortTime()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'time_format' => 'short',
    ));
    $this->assertEquals('2010-02-03 04:05:00', $transformer->reverseTransform('03.02.2010 04:05'));
  }

  public function testReverseTransformMediumTime()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'time_format' => 'medium',
    ));
    $this->assertEquals('2010-02-03 04:05:06', $transformer->reverseTransform('03.02.2010 04:05:06'));
  }

  public function testReverseTransformLongTime()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'time_format' => 'long',
    ));
    $this->assertEquals('2010-02-03 04:05:06', $transformer->reverseTransform('03.02.2010 04:05:06 GMT+00:00'));
  }

  public function testReverseTransformFullTime()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'time_format' => 'full',
    ));
    $this->assertEquals('2010-02-03 04:05:06', $transformer->reverseTransform('03.02.2010 04:05:06 GMT+00:00'));
  }

  public function testReverseTransformFromDifferentLocale()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $this->assertEquals('2010-02-03 04:05:00', $transformer->reverseTransform('Feb 3, 2010 04:05 AM'));
  }

  public function testReverseTransformFromDifferentTimezone()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05:00 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));

    $this->assertEquals($dateTime->format('Y-m-d H:i:s'), $transformer->reverseTransform('03.02.2010 04:05'));
  }

  public function testReverseTransformToDifferentTimezone()
  {
    $transformer = new DateTimeToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));
    $dateTime = new \DateTime('2010-02-03 04:05:00 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));
    $this->assertEquals($dateTime->format('Y-m-d H:i:s'), $transformer->reverseTransform('03.02.2010 04:05'));
  }

  public function testReverseTransformRequiresString()
  {
    $transformer = new DateTimeToStringTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform(12345);
  }

  public function testReverseTransformWrapsIntlErrors()
  {
    $transformer = new DateTimeToStringTransformer();

    $this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    $transformer->reverseTransform('12345');
  }

  public function testValidateDateFormatOption()
  {
    $this->setExpectedException('\InvalidArgumentException');

    new DateTimeToStringTransformer(array('date_format' => 'foobar'));
  }

  public function testValidateTimeFormatOption()
  {
    $this->setExpectedException('\InvalidArgumentException');

    new DateTimeToStringTransformer(array('time_format' => 'foobar'));
  }
}
