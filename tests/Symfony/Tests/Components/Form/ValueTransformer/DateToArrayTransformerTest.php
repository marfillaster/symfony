<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\DateToArrayTransformer;


class DateToArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransform()
  {
    $transformer = new DateToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));

    $output = array(
      'year' => 2010,
      'month' => 2,
      'day' => 3,
    );

    $this->assertEquals($output, $transformer->transform('2010-02-03'));
  }

  public function testTransformToDifferentTimezone()
  {
    $transformer = new DateToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));

    $dateTime = new \DateTime('2010-02-03 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));
    $output = array(
      'year' => (int)$dateTime->format('Y'),
      'month' => (int)$dateTime->format('m'),
      'day' => (int)$dateTime->format('d'),
    );

    $this->assertEquals($output, $transformer->transform('2010-02-03'));
  }

  public function testTransformFromDifferentTimezone()
  {
    $transformer = new DateToArrayTransformer(array(
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));

    $dateTime = new \DateTime('2010-02-03 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));
    $output = array(
      'year' => (int)$dateTime->format('Y'),
      'month' => (int)$dateTime->format('m'),
      'day' => (int)$dateTime->format('d'),
    );

    $this->assertEquals($output, $transformer->transform('2010-02-03'));
  }

  public function testTransformRequiresValidTime()
  {
    $transformer = new DateToArrayTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('12345'); // missing seconds
  }

  public function testReverseTransform()
  {
    $transformer = new DateToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'UTC',
    ));

    $input = array(
      'year' => 2010,
      'month' => 2,
      'day' => 3,
    );

    $this->assertEquals('2010-02-03', $transformer->reverseTransform($input));
  }

  public function testReverseTransformFromDifferentTimezone()
  {
    $transformer = new DateToArrayTransformer(array(
      'input_timezone' => 'UTC',
      'output_timezone' => 'Europe/Vienna',
    ));

    $input = array(
      'year' => 2010,
      'month' => 2,
      'day' => 3,
    );
    $dateTime = new \DateTime('2010-02-03 Europe/Vienna');
    $dateTime->setTimezone(new \DateTimeZone('UTC'));

    $this->assertEquals($dateTime->format('Y-m-d'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformToDifferentTimezone()
  {
    $transformer = new DateToArrayTransformer(array(
      'input_timezone' => 'Europe/Vienna',
      'output_timezone' => 'UTC',
    ));

    $input = array(
      'year' => 2010,
      'month' => 2,
      'day' => 3,
    );
    $dateTime = new \DateTime('2010-02-03 UTC');
    $dateTime->setTimezone(new \DateTimeZone('Europe/Vienna'));

    $this->assertEquals($dateTime->format('Y-m-d'), $transformer->reverseTransform($input));
  }

  public function testReverseTransformRequiresArray()
  {
    $transformer = new DateToArrayTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform('12345');
  }
}
