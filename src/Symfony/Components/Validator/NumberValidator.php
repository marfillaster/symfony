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
 * NumberValidator validates a number (integer or float). It also converts the input value to a float.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: NumberValidator.php 164 2010-01-24 18:07:51Z flo $
 */
class NumberValidator extends BaseValidator
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * max: The maximum value allowed
   *  * min: The minimum value allowed
   *
   * Available error codes:
   *
   *  * max
   *  * min
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see BaseValidator
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('max', '"%value%" must be at most %max%.');
    $this->addMessage('min', '"%value%" must be at least %min%.');

    $this->addOption('min');
    $this->addOption('max');

    $this->setMessage('invalid', '"%value%" is not a number.');
  }

  /**
   * Validates the value.
   *
   * @param  mixed $value   The value that should be validated
   * @throws InvalidArgumentException when the argument is not of the
   *                                  expected type
   * @throws ValidatorError when the validation fails
   */
  protected function doValidate($value)
  {
    if (!is_numeric($value))
    {
      throw new \InvalidArgumentException($this->getMessage('invalid', array('value' => $value)));
    }

    $number = floatval($value);

    if ($this->hasOption('max') && $number > $this->getOption('max'))
    {
      throw new ValidatorError($this->getMessage('max', array('value' => $value, 'max' => $this->getOption('max'))));
    }

    if ($this->hasOption('min') && $number < $this->getOption('min'))
    {
      throw new ValidatorError($this->getMessage('min', array('value' => $value, 'min' => $this->getOption('min'))));
    }
    
  }
}
