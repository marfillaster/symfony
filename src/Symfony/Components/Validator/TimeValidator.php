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
 * Validates whether a value is a time string of format "HH:MM:SS".
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Fabian Lange <fabian.lange@symfony-project.com>
 * @author     Florian Eckerstorfer <florian@eckerstorfer.org>
 * @version    SVN: $Id: TimeValidator.php 159 2010-01-24 12:06:04Z flo $
 */
class TimeValidator extends BaseValidator
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * min:  The minimum allowed time (as timestamp or string parsable by strtotime())
   *  * max:  The maximum allowed time (as timestamp or string parsable by strtotime())
   *
   * Available error codes:
   *
   *  * min
   *  * max   
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see BaseValidator
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('max', 'The time must be before %max%.');
    $this->addMessage('min', 'The time must be after %min%.');
    
    $this->addOption('min', null);
    $this->addOption('max', null);
  }
  
  protected function doValidate($value)
  {
    if (!preg_match('/(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/', $value))
    {
      throw new ValidatorError($this->getMessage('invalid'));
    }
    
    $value = str_replace(':', '', $value);
    // check max
    if ($max = $this->getOption('max'))
    {
      // convert timestamp to time number format
      if (is_numeric($max))
      {
        $maxError = date('H:i:s', $max);
        $max      = date('His', $max);
      }
      // convert string to date number
      else
      {
        $dateMax  = new \DateTime($max);
        $max      = $dateMax->format('His');
        $maxError = $dateMax->format('H:i:s');
      }
      
      if ($value > $max)
      {
        throw new ValidatorError($this->getMessage('max', array('value' => $value, 'max' => $maxError)));
      }
    }

    // check min
    if ($min = $this->getOption('min'))
    {
      // convert timestamp to date number
      if (is_numeric($min))
      {
        $minError = date('H:i:s', $min);
        $min      = date('His', $min);
      }
      // convert string to date number
      else
      {
        $dateMin  = new \DateTime($min);
        $min      = $dateMin->format('His');
        $minError = $dateMin->format('H:i:s');
      }
      
      if ($value < $min)
      {
        throw new ValidatorError($this->getMessage('min', array('value' => $value, 'min' => $minError)));
      }
    }
  }

}
