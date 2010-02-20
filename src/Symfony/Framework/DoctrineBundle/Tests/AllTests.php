<?php

namespace Symfony\Framework\DoctrineBundle\Tests;

require_once 'PHPUnit/Framework.php';
require_once __DIR__.'/SampleTest.php';

class AllTests
{
  public static function suite()
  {
    $suite = new \PHPUnit_Framework_TestSuite('DoctrineBundle');

    $suite->addTestSuite('\Symfony\Framework\DoctrineBundle\Tests\SampleTest');

    return $suite;
  }
}