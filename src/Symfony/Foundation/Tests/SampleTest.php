<?php

namespace Symfony\Foundation\Tests;

require_once __DIR__ . '/TestInit.php';

class SampleTest extends \PHPUnit_Framework_TestCase
{
  public function testTest()
  {
    $this->assertEquals(1, 1);
  }
}