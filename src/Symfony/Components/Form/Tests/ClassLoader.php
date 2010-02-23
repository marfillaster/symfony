<?php

/*
 * This file is part of the symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Components\Form\Tests;

/**
 * ClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * Based on http://groups.google.com/group/php-standards/web/psr-0-final-proposal
 *
 * Example usage:
 *
 *     [php]
 *     $loader = new ClassLoader();
 *     $loader->registerNamespace('Symfony', __DIR__.'/..');
 *     $loader->register();
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class ClassLoader
{
  protected $namespace;
  protected $namespaceLength;
  protected $includePath;

  /**
   * Creates a new loader for classes of the specified namespace.
   *
   * @param string $namespace   The namespace to use
   * @param string $includePath The path to the namespace
   */
  public function __construct($namespace, $includePath = null)
  {
    $this->namespace = $namespace;
    $this->namespaceLength = strlen($namespace);
    $this->includePath = $includePath;
  }

  /**
   * Installs this class loader on the SPL autoload stack.
   */
  public function register()
  {
    spl_autoload_register(array($this, 'loadClass'));
  }

  /**
   * Loads the given class or interface.
   *
   * @param string $className The name of the class to load
   */
  public function loadClass($className)
  {
    if (substr($className, 0, $this->namespaceLength) != $this->namespace)
    {
      return;
    }

    if (false !== ($lastNsPos = strripos($className, '\\')))
    {
      $namespace = substr($className, $this->namespaceLength + 1, $lastNsPos - $this->namespaceLength - 1);
      $className = substr($className, $lastNsPos + 1);
      $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
    }
    else
    {
      $namespace = '';
      $fileName = '';
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';

    require $this->includePath.DIRECTORY_SEPARATOR.$fileName;
  }
}
