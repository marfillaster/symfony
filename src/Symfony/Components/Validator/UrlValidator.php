<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Form\Callable;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * UrlValidator validates Urls.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: UrlValidator.php 184 2010-01-25 13:10:01Z bernhard $
 */
class UrlValidator extends RegexValidator
{
  const REGEX_URL_FORMAT = '~^
      (%s)://                                 # protocol
      (
        ([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
          |                                   #  or
        \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
      )
      (:[0-9]+)?                              # a port (optional)
      (/?|/\S+)                               # a /, nothing or a / with something
    $~ix';

  /**
   * Available options:
   *
   *  * protocols: An array of acceptable URL protocols (http, https, ftp and ftps by default)
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see RegexValidator
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addOption('protocols', array('http', 'https', 'ftp', 'ftps'));
    //FIXME: Fix Callable
    $this->setOption('pattern', new Callable(array($this, 'generateRegex')));
  }

  /**
   * Generates the current validator's regular expression.
   *
   * @return string
   */
  public function generateRegex()
  {
    return sprintf(self::REGEX_URL_FORMAT, implode('|', $this->getOption('protocols')));
  }
}
