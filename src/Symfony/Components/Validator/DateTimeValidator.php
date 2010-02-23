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
 * Validates whether a value is a datetime string of format "YYYY-MM-DD HH:MM:SS".
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Florian Eckerstorfer <florian@eckerstorfer.org>
 * @version    SVN: $Id: DateTimeValidator.php 159 2010-01-24 12:06:04Z flo $
 */
class DateTimeValidator extends BaseValidator
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * max:                     The maximum date allowed (as a timestamp or accecpted date() format)
   *  * min:                     The minimum date allowed (as a timestamp or accecpted date() format)
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
    $this->addMessage('max', 'The date must be before %max%.');
    $this->addMessage('min', 'The date must be after %min%.');

    $this->addOption('min', null);
    $this->addOption('max', null);
  }
  
  /**
   * Validates whether a value is a datetime string of format "YYYY-MM-DD HH:MM:SS".
   *
   * @param  string $value Datetime string of format "YYYY-MM-DD HH:MM:SS".
   * @return void
   * @throws ValidatorError when $value is not a date time.
   */
  protected function doValidate($value)
  {
    if (!preg_match('/^(\d{4})-((02-(0[1-9]|[12][0-9]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $value))
    {
      throw new ValidatorError($this->getMessage('invalid'));
    }
    
    $value = str_replace('-', '', $value);
    
    // check max
    if ($max = $this->getOption('max'))
    {
      // convert timestamp to date number format
      if (is_numeric($max))
      {
        $max      = date('YmdHis', $max);
        $maxError = date('Y-m-d H:i:s', $max);
      }
      // convert string to date number
      else
      {
        $dateMax  = new \DateTime($max);
        $max      = $dateMax->format('YmdHis');
        $maxError = $dateMax->format('Y-m-d H:i:s');
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
        $min      = date('YmdHis', $min);
        $minError = date('Y-m-d H:i:s', $min);
      }
      // convert string to date number
      else
      {
        $dateMin  = new \DateTime($min);
        $min      = $dateMin->format('YmdHis');
        $minError = $dateMin->format('Y-m-d H:i:s');
      }
      
      if ($value < $min)
      {
        throw new ValidatorError($this->getMessage('min', array('value' => $value, 'min' => $minError)));
      }
    }
  }
}
