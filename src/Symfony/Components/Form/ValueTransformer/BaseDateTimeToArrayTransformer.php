<?php

namespace Symfony\Components\Form\ValueTransformer;

use \Symfony\Components\Form\ValueTransformer\ValueTransformerException;

/**
 * Transforms between a normalized time and a localized time string/array.
 *
 * Options:
 *
 *  * "input": The type of the normalized format ("time" or "timestamp"). Default: "datetime"
 *  * "output": The type of the transformed format ("string" or "array"). Default: "string"
 *  * "format": The format of the time string ("short", "medium", "long" or "full"). Default: "short"
 *  * "locale": The locale of the localized string. Default: Result of Locale::getDefault()
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 */
abstract class BaseDateTimeToArrayTransformer extends BaseDateTimeTransformer
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    parent::configure();

    $this->addOption('input_timezone', 'UTC');
    $this->addOption('output_timezone', date_default_timezone_get());
  }

  /**
   * Transforms a normalized date into a localized date string/array.
   *
   * @param  number $value  Normalized date.
   * @return string|array   Localized date string/array.
   */
  protected function fromDateTime(\DateTime $dateTime)
  {
    $inputTimezone = $this->getOption('input_timezone');
    $outputTimezone = $this->getOption('output_timezone');

    if ($inputTimezone != $outputTimezone)
    {
      $dateTime->setTimezone(new \DateTimeZone($outputTimezone));
    }

    return array(
      'year'    => (int)$dateTime->format('Y'),
      'month'   => (int)$dateTime->format('m'),
      'day'     => (int)$dateTime->format('d'),
      'hour'    => (int)$dateTime->format('H'),
      'minute'  => (int)$dateTime->format('i'),
      'second'  => (int)$dateTime->format('s'),
    );
  }

  /**
   * Transforms a localized date string/array into a normalized date.
   *
   * @param string|array $value Localized date string/array
   * @return string Normalized date
   */
  protected function toDateTime($value)
  {
    $inputTimezone = $this->getOption('input_timezone');
    $outputTimezone = $this->getOption('output_timezone');

    if (!is_array($value))
    {
      throw new \InvalidArgumentException(sprintf('Expected argument of type array, %s given', gettype($value)));
    }

    $dateTime = new \DateTime(sprintf(
      '%s-%s-%s %s:%s:%s %s',
      isset($value['year']) ? $value['year'] : 1970,
      isset($value['month']) ? $value['month'] : 1,
      isset($value['day']) ? $value['day'] : 1,
      isset($value['hour']) ? $value['hour'] : 0,
      isset($value['minute']) ? $value['minute'] : 0,
      isset($value['second']) ? $value['second'] : 0,
      $outputTimezone
    ));

    if ($inputTimezone != $outputTimezone)
    {
      $dateTime->setTimezone(new \DateTimeZone($inputTimezone));
    }

    return $dateTime;
  }
}