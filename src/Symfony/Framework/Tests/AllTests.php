<?php

namespace Symfony\Framework\Tests;

require_once 'PHPUnit/Framework.php';

class AllTests
{
  public static function suite()
  {
    $suite = new \PHPUnit_Framework_TestSuite('Framework');

    $suite->addTestFiles(glob(__DIR__.'/../*Bundle/Tests/AllTests.php'));

    return $suite;
  }
}