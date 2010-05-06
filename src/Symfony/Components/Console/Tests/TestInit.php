<?php

/*
 * This file bootstraps the test environment.
 */

if (!isset($_SERVER['SYMFONY']))
{
  throw new \RuntimeException(
<<<EOF
Please set the environment variable SYMFONY to point to the Symfony 2 src/ directory.
On Unix, you can use the command
  export SYMFONY=/path/to/symfony/src
On Windows, you can use the command
  set SYMFONY=\path\to\symfony\src

EOF
  );
}

require_once $_SERVER['SYMFONY'] . '/Symfony/Tests/TestInit.php';