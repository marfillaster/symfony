<?php

namespace Symfony\Tests\Components\Form\ValueTransformer;

require_once __DIR__ . '/../../../bootstrap.php';

use Symfony\Components\Form\ValueTransformer\PercentToStringTransformer;


class PercentToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
  public function testTransform()
  {
    $transformer = new PercentToStringTransformer(array(
      'locale' => 'de_AT',
    ));

    $this->assertEquals('10 %', $transformer->transform(0.1));
    $this->assertEquals('15 %', $transformer->transform(0.15));
    $this->assertEquals('12 %', $transformer->transform(0.1234));
    $this->assertEquals('200 %', $transformer->transform(2));
  }

  public function testTransformWithInteger()
  {
    $transformer = new PercentToStringTransformer(array(
      'locale' => 'de_AT',
      'type' => 'integer',
    ));

    $this->assertEquals('0 %', $transformer->transform(0.1));
    $this->assertEquals('1 %', $transformer->transform(1));
    $this->assertEquals('15 %', $transformer->transform(15));
    $this->assertEquals('16 %', $transformer->transform(15.9));
  }

  public function testTransformWithPrecision()
  {
    $transformer = new PercentToStringTransformer(array(
      'locale' => 'de_AT',
      'precision' => 2,
    ));

    $this->assertEquals('12,34 %', $transformer->transform(0.1234));
  }

  public function testReverseTransform()
  {
    $transformer = new PercentToStringTransformer(array(
      'locale' => 'de_AT',
    ));

    $this->assertEquals(0.1, $transformer->reverseTransform('10 %'));
    $this->assertEquals(0.15, $transformer->reverseTransform('15 %'));
    $this->assertEquals(0.12, $transformer->reverseTransform('12 %'));
    $this->assertEquals(2, $transformer->reverseTransform('200 %'));
  }

  public function testReverseTransformWithInteger()
  {
    $transformer = new PercentToStringTransformer(array(
      'locale' => 'de_AT',
      'type' => 'integer',
    ));

    $this->assertEquals(10, $transformer->reverseTransform('10 %'));
    $this->assertEquals(15, $transformer->reverseTransform('15 %'));
    $this->assertEquals(12, $transformer->reverseTransform('12 %'));
    $this->assertEquals(200, $transformer->reverseTransform('200 %'));
  }

  public function testTransformExpectsNumeric()
  {
    $transformer = new PercentToStringTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->transform('foo');
  }

  public function testReverseTransformExpectsString()
  {
    $transformer = new PercentToStringTransformer();

    $this->setExpectedException('\InvalidArgumentException');

    $transformer->reverseTransform(1);
  }
}
