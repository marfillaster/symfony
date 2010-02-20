<?php

namespace Symfony\Foundation\Tests;

require_once 'PHPUnit/Framework.php';
require_once __DIR__.'/SampleTest.php';

class AllTests
{
  public static function suite()
  {
    $suite = new \PHPUnit_Framework_TestSuite('Foundation');

    $suite->addTestSuite('\Symfony\Foundation\Tests\SampleTest');

    return $suite;
  }
}