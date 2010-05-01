<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\TimeToArrayTransformer;


class TimeToArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransform()
  {
    $transformer = new TimeToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $output = array(
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );

    $this->assertEquals($output, $transformer->transform('04:05:06'));
  }

  public function testTransformToDifferentTimezone()
  {
    $transformer = new TimeToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));
    $dateTime = new \DateTime('04:05:06 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));
    $output = array(
      'hour' => (int)$dateTime->format('H'),
      'minute' => (int)$dateTime->format('i'),
      'second' => (int)$dateTime->format('s'),
    );

    $this->assertEquals($output, $transformer->transform('04:05:06'));
  }

  public function testTransformFromDifferentTimezone()
  {
    $transformer = new TimeToArrayTransformer(array(
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));
    $dateTime = new \DateTime('06:05:06 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));
    $output = array(
      'hour' => (int)$dateTime->format('H'),
      'minute' => (int)$dateTime->format('i'),
      'second' => (int)$dateTime->format('s'),
    );

    $this->assertEquals($output, $transformer->transform('06:05:06'));
  }

  public function testTransformRequiresValidTime()
  {
    $transformer = new TimeToArrayTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('12345'); // missing seconds
  }

  public function testReverseTransform()
  {
    $transformer = new TimeToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));
    $input = array(
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );

    $this->assertEquals('04:05:06', $transformer->reverseTransform($input));
  }

  public function testReverseTransformFromDifferentTimezone()
  {
    $transformer = new TimeToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));
    $input = array(
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );
    $dateTime = new \DateTime('1970-01-01 04:05:06 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));

    $this->assertEquals($dateTime->format('H:i:s'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformToDifferentTimezone()
  {
    $transformer = new TimeToArrayTransformer(array(
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));
    $input = array(
      'hour' => 4,
      'minute' => 5,
      'second' => 6,
    );
    $dateTime = new \DateTime('1970-01-01 04:05:06 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));
    $this->assertEquals($dateTime->format('H:i:s'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformRequiresArray()
  {
    $transformer = new TimeToArrayTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform('12345');
  }
}
