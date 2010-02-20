<?php

namespace Symfony\Components\Console\Tests;

require_once 'PHPUnit/Framework.php';
require_once __DIR__.'/SampleTest.php';

class AllTests
{
  public static function suite()
  {
    $suite = new \PHPUnit_Framework_TestSuite('Console');

    $suite->addTestSuite('\Symfony\Components\Console\Tests\SampleTest');

    return $suite;
  }
}