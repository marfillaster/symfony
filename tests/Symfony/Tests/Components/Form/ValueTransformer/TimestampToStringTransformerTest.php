<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\TimestampToStringTransformer;


class TimestampToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransformShortDate()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'short',
      'time_format' => 'none',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');

    $this->assertEquals('03.02.10', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformMediumDate()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'medium',
      'time_format' => 'none',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');

    $this->assertEquals('03.02.2010', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformLongDate()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'long',
      'time_format' => 'none',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');

    $this->assertEquals('03. Februar 2010', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformFullDate()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'full',
      'time_format' => 'none',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');

    $this->assertEquals('Mittwoch, 03. Februar 2010', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformShortTime()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'none',
      'time_format' => 'short',
    ));

    $dateTime = new \DateTime('1970-01-01 04:05:06 UTC');

    $this->assertEquals('04:05', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformMediumTime()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'none',
      'time_format' => 'medium',
    ));

    $dateTime = new \DateTime('1970-01-01 04:05:06 UTC');

    $this->assertEquals('04:05:06', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformLongTime()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'none',
      'time_format' => 'long',
    ));

    $dateTime = new \DateTime('1970-01-01 04:05:06 UTC');

    $this->assertEquals('04:05:06 GMT+00:00', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformFullTime()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'none',
      'time_format' => 'full',
    ));

    $dateTime = new \DateTime('1970-01-01 04:05:06 UTC');

    $this->assertEquals('04:05:06 GMT+00:00', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformToDifferentLocale()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05:06 UTC');

    $this->assertEquals('Feb 3, 2010 4:05 AM', $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformToDifferentTimezone()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05:06 UTC');
    $input = (int)$dateTime->format('U');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));

    $this->assertEquals($dateTime->format('d.m.Y H:i'), $transformer->transform($input));
  }

  public function testTransformFromDifferentTimezone()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05:06 Europe/Vienna');
    $input = (int)$dateTime->format('U');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));

    $this->assertEquals($dateTime->format('d.m.Y H:i'), $transformer->transform($input));
  }

  public function testTransformRequiresValidDate()
  {
    $transformer = new TimestampToStringTransformer(array(
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('2010-01'); // missing days
  }

  public function testTransformWrapsIntlErrors()
  {
    $transformer = new TimestampToStringTransformer(array(
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    // HOW TO REPRODUCE?

    //$this->setExpectedException('Symfony\Components\Form\ValueTransformer\Transdate_formationFailedException');

    //$transformer->transform(1.5);
  }

  public function testReverseTransformShortDate()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'short',
      'time_format' => 'none',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform('03.02.10'));
  }

  public function testReverseTransformMediumDate()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'medium',
      'time_format' => 'none',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform('03.02.2010'));
  }

  public function testReverseTransformLongDate()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'long',
      'time_format' => 'none',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform('03. Februar 2010'));
  }

  public function testReverseTransformFullDate()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'full',
      'time_format' => 'none',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform('Mittwoch, 03. Februar 2010'));
  }

  public function testReverseTransformFromDifferentLocale()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'en_US',
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05 UTC');

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform('Feb 3, 2010 4:05 AM'));
  }

  public function testReverseTransformFromDifferentTimezone()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05 Europe/Vienna');
    $input = $dateTime->format('d.m.Y H:i');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformToDifferentTimezone()
  {
    $transformer = new TimestampToStringTransformer(array(
      'locale' => 'de_AT',
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05 UTC');
    $input = $dateTime->format('d.m.Y H:i');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformRequiresString()
  {
    $transformer = new TimestampToStringTransformer(array(
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform(12345);
  }

  public function testReverseTransformWrapsIntlErrors()
  {
    $transformer = new TimestampToStringTransformer(array(
      'date_format' => 'medium',
      'time_format' => 'short',
    ));

    $this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    $transformer->reverseTransform('12345');
  }

  public function testValidateDateFormatOption()
  {
    $this->setExpectedException('\InvalidArgumentException');

    new TimestampToStringTransformer(array(
      'date_format' => 'foobar',
      'time_format' => 'short',
    ));
  }

  public function testValidateTimeFormatOption()
  {
    $this->setExpectedException('\InvalidArgumentException');

    new TimestampToStringTransformer(array(
      'date_format' => 'medium',
      'time_format' => 'foobar',
    ));
  }
}
