<?php

namespace Symfony\Components\Form\Tests;

require_once 'PHPUnit/Framework.php';

class AllTests
{
  public static function suite()
  {
    $suite = new \PHPUnit_Framework_TestSuite('Components');

    $directoryIterator = new \RecursiveDirectoryIterator(__DIR__);
    $recursiveIterator = new \RecursiveIteratorIterator($directoryIterator);
    $filteredIterator = new \RegexIterator($recursiveIterator, '/Test\.php$/');

    $suite->addTestFiles(iterator_to_array($filteredIterator));

    return $suite;
  }
}