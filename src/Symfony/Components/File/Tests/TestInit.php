<?php

/*
 * This file bootstraps the test environment.
 */
namespace Symfony\Components\File\Tests;

error_reporting(E_ALL | E_STRICT);

require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/ClassLoader.php';

$classLoader = new ClassLoader(
  'Symfony\Components',
  __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
);
$classLoader->register();
