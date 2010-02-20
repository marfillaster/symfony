<?php

namespace Symfony\Components\Tests;

require_once 'PHPUnit/Framework.php';

class AllTests
{
  public static function suite()
  {
    $suite = new \PHPUnit_Framework_TestSuite('Components');

    $suite->addTestFiles(glob(__DIR__.'/../*/Tests/AllTests.php'));

    return $suite;
  }
}