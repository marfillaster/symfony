<?php

namespace Symfony\Components\Validator;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * EmailValidator validates emails.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: EmailValidator.php 159 2010-01-24 12:06:04Z flo $
 */
class EmailValidator extends RegexValidator
{
  const REGEX_EMAIL = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';

  /**
   * Check DNA Records for MX type (from Doctrine EmailValidator)
   *
   * @param string $host Host name
   * @return boolean
   * @licence This software consists of voluntary contributions made by many individuals
   * and is licensed under the LGPL. For more information, see
   * <http://www.phpdoctrine.org>.
   */
  private function _checkMX($host)
  {
    // We have different behavior here depending of OS and PHP version
    if (strtolower(substr(PHP_OS, 0, 3)) == 'win' && version_compare(PHP_VERSION, '5.3.0', '<')) {
      $output = array();
            
      @exec('nslookup -type=MX '.escapeshellcmd($host) . ' 2>&1', $output);
            
      if (empty($output))
      {
        throw new ValidatorError('Unable to execute DNS lookup. Are you sure PHP can call exec()?');
      }

      foreach ($output as $line)
      {
        if (preg_match('/^'.$host.'/', $line))
        {
          return true;
        }
      }
            
      return false;
    }
    else if (function_exists('checkdnsrr'))
    {
      return checkdnsrr($host, 'MX');
    }
        
    throw new ValidatorError('Could not retrieve DNS record information. Remove check_mx = true to prevent this warning');
  }
  
  /**
   * @see RegexValidator
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setOption('pattern', self::REGEX_EMAIL);
    
    $this->addOption('check_mx');
    $this->addMessage('check_mx', 'The domain "%value%" doesn\'t exist.');
  }

  /**
   * Validates the value.
   *
   * @param  mixed $value   The value that should be validated
   * @throws ValidatorError when the validation fails
   */
  protected function doValidate($value)
  {
    $string = (string) $value;
    
    parent::doValidate($value);
    
    if ($this->hasOption('check_mx') && $this->getOption('check_mx')==true)
    {
      list($user,$host) = explode('@', $string);
      if(!$this->_checkMX($host))
      {
        // validation wasn't succcessfull
        throw new ValidatorError($this->getMessage('check_mx', array('value' => $value, 'check_mx' => $this->getOption('check_mx'))));
      }
    }
  }
}
