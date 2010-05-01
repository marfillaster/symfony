<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\TimestampToArrayTransformer;


class TimestampToArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransform()
  {
    $transformer = new TimestampToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));

    $dateTime = new \DateTime('2010-02-03 04:05:06 UTC');
    $output = array(
      'year' => 2010,
      'month' => 2,
      'day' => 3,
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );

    $this->assertEquals($output, $transformer->transform((int)$dateTime->format('U')));
  }

  public function testTransformToDifferentTimezone()
  {
    $transformer = new TimestampToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));

    $dateTime = new \DateTime('04:05:06 UTC');
    $input = (int)$dateTime->format('U');

    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));
    $output = array(
      'year' => (int)$dateTime->format('Y'),
      'month' => (int)$dateTime->format('m'),
      'day' => (int)$dateTime->format('d'),
      'hour' => (int)$dateTime->format('H'),
      'minute' => (int)$dateTime->format('i'),
      'second' => (int)$dateTime->format('s'),
    );

    $this->assertEquals($output, $transformer->transform($input));
  }

  public function testTransformFromDifferentTimezone()
  {
    $transformer = new TimestampToArrayTransformer(array(
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));

    $dateTime = new \DateTime('06:05:06 Europe/Vienna');
    $input = (int)$dateTime->format('U');

    $dateTime->setTimezone(new \DateTimeZone('UTC'));
    $output = array(
      'year' => (int)$dateTime->format('Y'),
      'month' => (int)$dateTime->format('m'),
      'day' => (int)$dateTime->format('d'),
      'hour' => (int)$dateTime->format('H'),
      'minute' => (int)$dateTime->format('i'),
      'second' => (int)$dateTime->format('s'),
    );

    $this->assertEquals($output, $transformer->transform($input));
  }

  public function testTransformRequiresValidTime()
  {
    $transformer = new TimestampToArrayTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('12345'); // missing seconds
  }

  public function testReverseTransform()
  {
    $transformer = new TimestampToArrayTransformer(array(
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
    $dateTime = new \DateTime('2010-02-03 04:05:06 UTC');

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformFromDifferentTimezone()
  {
    $transformer = new TimestampToArrayTransformer(array(
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

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformToDifferentTimezone()
  {
    $transformer = new TimestampToArrayTransformer(array(
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

    $this->assertEquals((int)$dateTime->format('U'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformRequiresArray()
  {
    $transformer = new TimestampToArrayTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform('12345');
  }
}
