<?php

/*
 * This file bootstraps the test environment.
 */
namespace Symfony\Tests;

error_reporting(E_ALL | E_STRICT);

require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/../Foundation/ClassLoader.php';

$classLoader = new \Symfony\Foundation\ClassLoader();
$classLoader->registerNamespace('Symfony', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
$classLoader->register();
