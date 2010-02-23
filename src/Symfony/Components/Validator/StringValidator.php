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
 * StringValidator validates a string. It also converts the input value to a string.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: StringValidator.php 159 2010-01-24 12:06:04Z flo $
 */
class StringValidator extends BaseValidator
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * max_length: The maximum length of the string
   *  * min_length: The minimum length of the string
   *
   * Available error codes:
   *
   *  * max_length
   *  * min_length
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see BaseValidator
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('max_length', '"%value%" is too long (%max_length% characters max).');
    $this->addMessage('min_length', '"%value%" is too short (%min_length% characters min).');

    $this->addOption('max_length');
    $this->addOption('min_length');

    $this->setOption('empty_value', '');
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

    $length = function_exists('mb_strlen') ? mb_strlen($string, $this->getCharset()) : strlen($string);

    if ($this->hasOption('max_length') && $length > $this->getOption('max_length'))
    {
      throw new ValidatorError($this->getMessage('max_length', array('value' => $value, 'max_length' => $this->getOption('max_length'))));
    }

    if ($this->hasOption('min_length') && $length < $this->getOption('min_length'))
    {
      throw new ValidatorError($this->getMessage('min_length', array('value' => $value, 'min_length' => $this->getOption('min_length'))));
    }
  }
}
