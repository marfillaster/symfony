<?php

namespace Symfony\Framework\Tests;

require_once 'PHPUnit/Framework.php';

class AllTests
{
  public static function suite()
  {
    $suite = new \PHPUnit_Framework_TestSuite('Framework');

    $suites = glob(__DIR__.'/../*Bundle/Tests/AllTests.php');
    foreach ($suites as $key => $path)
    {
      $path = realpath($path);
      require_once $path;
      
      $pos = strpos($path, '/Symfony/');
      $tmp = substr($path, $pos, strlen($path) - ($pos + 4));
      $className = str_replace('/', '\\', $tmp);
      $suite->addTest($className::suite());
    }

    return $suite;
  }
}