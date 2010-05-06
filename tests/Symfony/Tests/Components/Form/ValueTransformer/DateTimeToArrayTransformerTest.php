<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\DateTimeToArrayTransformer;


class DateTimeToArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransform()
  {
    $transformer = new DateTimeToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));

    $output = array(
      'year' => 2010,
      'month' => 2,
      'day' => 3,
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );

    $this->assertEquals($output, $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformToDifferentTimezone()
  {
    $transformer = new DateTimeToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05:06 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));
    $output = array(
      'year' => (int)$dateTime->format('Y'),
      'month' => (int)$dateTime->format('m'),
      'day' => (int)$dateTime->format('d'),
      'hour' => (int)$dateTime->format('H'),
      'minute' => (int)$dateTime->format('i'),
      'second' => (int)$dateTime->format('s'),
    );

    $this->assertEquals($output, $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformFromDifferentTimezone()
  {
    $transformer = new DateTimeToArrayTransformer(array(
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05:06 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));
    $output = array(
      'year' => (int)$dateTime->format('Y'),
      'month' => (int)$dateTime->format('m'),
      'day' => (int)$dateTime->format('d'),
      'hour' => (int)$dateTime->format('H'),
      'minute' => (int)$dateTime->format('i'),
      'second' => (int)$dateTime->format('s'),
    );

    $this->assertEquals($output, $transformer->transform('2010-02-03 04:05:06'));
  }

  public function testTransformRequiresValidTime()
  {
    $transformer = new DateTimeToArrayTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('12345'); // missing seconds
  }

  public function testReverseTransform()
  {
    $transformer = new DateTimeToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));

    $input = array(
      'year' => 2010,
      'month' => 2,
      'day' => 3,
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );

    $this->assertEquals('2010-02-03 04:05:06', $transformer->reverseTransform($input));
  }

  public function testReverseTransformFromDifferentTimezone()
  {
    $transformer = new DateTimeToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));

    $input = array(
      'year' => 2010,
      'month' => 2,
      'day' => 3,
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );
    $dateTime = new \DateTime('2010-02-03 04:05:06 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));

    $this->assertEquals($dateTime->format('Y-m-d H:i:s'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformToDifferentTimezone()
  {
    $transformer = new DateTimeToArrayTransformer(array(
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));

    $input = array(
      'year' => 2010,
      'month' => 2,
      'day' => 3,
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );
    $dateTime = new \DateTime('2010-02-03 04:05:06 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));

    $this->assertEquals($dateTime->format('Y-m-d H:i:s'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformRequiresArray()
  {
    $transformer = new DateTimeToArrayTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform('12345');
  }
}
