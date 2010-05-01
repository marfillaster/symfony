<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\DateValueTransformer;


class DateValueTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransformDateToShortDateString()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'format' => 'short'));
    $this->assertEquals('2/3/10', $transformer->transform('2010-02-03'));
  }

  public function testTransformDateToMediumDateString()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'format' => 'medium'));
    $this->assertEquals('Feb 3, 2010', $transformer->transform('2010-02-03'));
  }

  public function testTransformDateToLongDateString()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'format' => 'long'));
    $this->assertEquals('February 3, 2010', $transformer->transform('2010-02-03'));
  }

  public function testTransformDateToFullDateString()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'format' => 'full'));
    $this->assertEquals('Wednesday, February 3, 2010', $transformer->transform('2010-02-03'));
  }

  public function testTransformTimestampToDateString()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'input' => 'timestamp'));
    $this->assertEquals('Feb 3, 2010', $transformer->transform(mktime(4, 5, 6, 2, 3, 2010)));
  }

  public function testTransformDateToArray()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'output' => 'array'));
    $output = array(
      'day' => 3,
      'month' => 2,
      'year' => 2010,
    );
    $this->assertEquals($output, $transformer->transform('2010-02-03'));
  }

  public function testUseDifferentLocale()
  {
    $transformer = new DateValueTransformer(array('locale' => 'de_DE'));
    $this->assertEquals('03.02.2010', $transformer->transform('2010-02-03'));
  }

  public function testTransformRequiresValidDate()
  {
    $transformer = new DateValueTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('2010-01'); // missing days
  }

  public function testTransformRequiresValidTimestamp()
  {
    $transformer = new DateValueTransformer(array('input' => 'timestamp'));

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('foobar'); // no integer
  }

  public function testTransformWrapsIntlErrors()
  {
    $transformer = new DateValueTransformer(array('input' => 'timestamp'));

    // HOW TO REPRODUCE?

    //$this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    //$transformer->transform(1.5);
  }

  public function testReverseTransformShortDateStringToDate()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'format' => 'short'));
    $this->assertEquals('2010-02-03', $transformer->reverseTransform('2/3/10'));
  }

  public function testReverseTransformMediumDateStringToDate()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'format' => 'medium'));
    $this->assertEquals('2010-02-03', $transformer->reverseTransform('Feb 3, 2010'));
  }

  public function testReverseTransformLongDateStringToDate()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'format' => 'long'));
    $this->assertEquals('2010-02-03', $transformer->reverseTransform('February 3, 2010'));
  }

  public function testReverseTransformFullDateStringToDate()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'format' => 'full'));
    $this->assertEquals('2010-02-03', $transformer->reverseTransform('Wednesday, February 3, 2010'));
  }

  public function testReverseTransformArrayToDate()
  {
    $transformer = new DateValueTransformer(array('locale' => 'en_US', 'output' => 'array'));
    $input = array(
      'day' => 3,
      'month' => 2,
      'year' => 2010,
    );
    $this->assertEquals('2010-02-03', $transformer->reverseTransform($input));
  }

  public function testReverseTransformRequiresString()
  {
    $transformer = new DateValueTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform(12345);
  }

  public function testReverseTransformRequiresArray()
  {
    $transformer = new DateValueTransformer(array('output' => 'array'));

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform('12345');
  }

  public function testReverseTransformWrapsIntlErrors()
  {
    $transformer = new DateValueTransformer();

    $this->setExpectedException('Symfony\Components\Form\ValueTransformer\TransformationFailedException');

    $transformer->reverseTransform('12345');
  }

  public function testValidateFormatOption()
  {
    $this->setExpectedException('\InvalidArgumentException');

    new DateValueTransformer(array('format' => 'foobar'));
  }
}
