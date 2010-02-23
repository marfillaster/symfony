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
 * Validates whether a value is a date string of format "YYYY-MM-DD".
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Florian Eckerstorfer <florian@eckerstorfer.org>
 * @version    SVN: $Id: DateValidator.php 233 2010-01-31 12:59:12Z robert $
 */
class DateValidator extends BaseValidator
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
  // 
  protected function doValidate($value)
  {
    if (!preg_match('/(\d{4})-((02-(0[1-9]|[12][0-9]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01])))/', $value))
    {
      throw new ValidatorError($this->getMessage('invalid'));
    }
    
    $value = str_replace('-', '', $value);
    
    // check max
    if (!is_null($max = $this->getOption('max')))
    {
      // convert timestamp to date number format
      if (is_numeric($max))
      {
        $max      = date('Ymd', $max);
        $maxError = date('Y-m-d', $max);
      }
      // convert string to date number
      else
      {
        $dateMax  = new \DateTime($max);
        $max      = $dateMax->format('Ymd');
        $maxError = $dateMax->format('Y-m-d');
      }
      
      if ($value > $max)
      {
        throw new ValidatorError($this->getMessage('max', array('value' => $value, 'max' => $maxError)));
      }
    }

    // check min
    if (!is_null($min = $this->getOption('min')))
    {
      // convert timestamp to date number
      if (is_numeric($min))
      {
        $min      = date('Ymd', $min);
        $minError = date('Y-m-d', $min);
      }
      // convert string to date number
      else
      {
        $dateMin  = new \DateTime($min);
        $min      = $dateMin->format('Ymd');
        $minError = $dateMin->format('Y-m-d');
      }
      
      if ($value < $min)
      {
        throw new ValidatorError($this->getMessage('min', array('value' => $value, 'min' => $minError)));
      }
    }
  }
  
  /**
   * @see BaseValidator
   */
  protected function doClean($value)
  {
    // check date format
    if ($regex = $this->getOption('date_format'))
    {
      if (!preg_match($regex, $value, $match))
      {
        throw new ValidatorError($this, 'bad_format', array('value' => $value, 'date_format' => $this->getOption('date_format_error') ? $this->getOption('date_format_error') : $this->getOption('date_format')));
      }

      $value = $match;
    }

    // convert array to date string
    if (is_array($value))
    {
      $value = $this->convertDateArrayToString($value);
    }

    // convert timestamp to date number format
    if (ctype_digit($value))
    {
      $cleanTime = (integer) $value;
      $clean     = date('YmdHis', $cleanTime);
    }
    // convert string to date number format
    else
    {
      try
      {
        //FIXME: Use of DateTime requires DateTimeZone
        $date  = new \DateTime($value, new \DateTimeZone(date_default_timezone_get()));
        $clean = $date->format('YmdHis');
      }
      catch (\Exception $e)
      {
        throw new ValidatorError($this, 'invalid', array('value' => $value));
      }
    }

    // check max
    if (!is_null($max = $this->getOption('max')))
    {
      // convert timestamp to date number format
      if (ctype_digit($max))
      {
        $max      = date('YmdHis', $max);
        $maxError = date($this->getOption('date_format_range_error'), $max);
      }
      // convert string to date number
      else
      {
        $dateMax  = new \DateTime($max, new \DateTimeZone(date_default_timezone_get()));
        $max      = $dateMax->format('YmdHis');
        $maxError = $dateMax->format($this->getOption('date_format_range_error'));
      }

      if ($clean > $max)
      {
        throw new ValidatorError($this, 'max', array('value' => $value, 'max' => $maxError));
      }
    }

    // check min
    if (!is_null($min = $this->getOption('min')))
    {
      // convert timestamp to date number
      if (ctype_digit($min))
      {
        $min      = date('YmdHis', $min);
        $minError = date($this->getOption('date_format_range_error'), $min);
      }
      // convert string to date number
      else
      {
        $dateMin  = new \DateTime($min);
        $min      = $dateMin->format('YmdHis');
        $minError = $dateMin->format($this->getOption('date_format_range_error'));
      }

      if ($clean < $min)
      {
        throw new ValidatorError($this, 'min', array('value' => $value, 'min' => $minError));
      }
    }

    if ($clean === $this->getEmptyValue())
    {
      return $cleanTime;
    }

    $format = $this->getOption('with_time') ? $this->getOption('datetime_output') : $this->getOption('date_output');

    return isset($date) ? $date->format($format) : date($format, $cleanTime);
  }

  /**
   * Converts an array representing a date to a timestamp.
   *
   * The array can contains the following keys: year, month, day, hour, minute, second
   *
   * @param  array $value  An array of date elements
   *
   * @return int A timestamp
   */
  protected function convertDateArrayToString($value)
  {
    // all elements must be empty or a number
    foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $key)
    {
      if (isset($value[$key]) && !preg_match('#^\d+$#', $value[$key]) && !empty($value[$key]))
      {
        throw new ValidatorError($this, 'invalid', array('value' => $value));
      }
    }

    // if one date value is empty, all others must be empty too
    $empties =
      (!isset($value['year']) || !$value['year'] ? 1 : 0) +
      (!isset($value['month']) || !$value['month'] ? 1 : 0) +
      (!isset($value['day']) || !$value['day'] ? 1 : 0)
    ;
    if ($empties > 0 && $empties < 3)
    {
      throw new ValidatorError($this, 'invalid', array('value' => $value));
    }
    else if (3 == $empties)
    {
      return $this->getEmptyValue();
    }

    if (!checkdate(intval($value['month']), intval($value['day']), intval($value['year'])))
    {
      throw new ValidatorError($this, 'invalid', array('value' => $value));
    }

    if ($this->getOption('with_time'))
    {
      // if second is set, minute and hour must be set
      // if minute is set, hour must be set
      if (
        $this->isValueSet($value, 'second') && (!$this->isValueSet($value, 'minute') || !$this->isValueSet($value, 'hour'))
        ||
        $this->isValueSet($value, 'minute') && !$this->isValueSet($value, 'hour')
      )
      {
        throw new ValidatorError($this, 'invalid', array('value' => $value));
      }

      $clean = sprintf(
        "%04d-%02d-%02d %02d:%02d:%02d",
        intval($value['year']),
        intval($value['month']),
        intval($value['day']),
        isset($value['hour']) ? intval($value['hour']) : 0,
        isset($value['minute']) ? intval($value['minute']) : 0,
        isset($value['second']) ? intval($value['second']) : 0
      );
    }
    else
    {
      $clean = sprintf(
        "%04d-%02d-%02d %02d:%02d:%02d",
        intval($value['year']),
        intval($value['month']),
        intval($value['day']),
        0,
        0,
        0
      );
    }

    return $clean;
  }

  protected function isValueSet($values, $key)
  {
    return isset($values[$key]) && !in_array($values[$key], array(null, ''), true);
  }

  /**
   * @see BaseValidator
   */
  protected function isEmpty($value)
  {
    if (is_array($value))
    {
      $filtered = array_filter($value);

      return empty($filtered);
    }

    return parent::isEmpty($value);
  }
}
