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
class TimeToArrayTransformer extends BaseDateTimeToArrayTransformer
{
  /**
   * Transforms a normalized date into a localized date string/array.
   *
   * @param  number $value  Normalized date.
   * @return string|array   Localized date string/array.
   */
  public function transform($value)
  {
    $inputTimezone = $this->getOption('input_timezone');

    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $value))
    {
      throw new \InvalidArgumentException('Expected string of format "HH:MM:SS"');
    }

    $array = $this->fromDateTime(new \DateTime("$value $inputTimezone"));

    unset($array['year'], $array['month'], $array['day']);

    return $array;
  }

  /**
   * Transforms a localized date string/array into a normalized date.
   *
   * @param string|array $value Localized date string/array
   * @return string Normalized date
   */
  public function reverseTransform($value)
  {
    return $this->toDateTime($value)->format('H:i:s');
  }
}