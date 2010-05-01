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
abstract class BaseDateTimeToStringTransformer extends BaseDateTimeTransformer
{
  protected $dateFormatOption = null;
  protected $timeFormatOption = null;

  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    parent::configure();

    $this->addOption('locale', \Locale::getDefault());
    $this->addOption('input_timezone', 'UTC');
    $this->addOption('output_timezone', date_default_timezone_get());

    if ($this->dateFormatOption)
    {
      if (!$this->getOption($this->dateFormatOption))
      {
        throw new \InvalidArgumentException(sprintf('The option "%s" is required', $this->dateFormatOption));
      }

      if (!in_array($this->getOption($this->dateFormatOption), self::$formats, true))
      {
        throw new \InvalidArgumentException(sprintf('The option "%s" is expected to be one of "%s". Is "%s"', $this->dateFormatOption, implode('", "', self::$formats), $this->getOption('time_format')));
      }
    }

    if ($this->timeFormatOption)
    {
      if (!$this->getOption($this->timeFormatOption))
      {
        throw new \InvalidArgumentException(sprintf('The option "%s" is required', $this->timeFormatOption));
      }

      if (!in_array($this->getOption($this->timeFormatOption), self::$formats, true))
      {
        throw new \InvalidArgumentException(sprintf('The option "%s" is expected to be one of "%s". Is "%s"', $this->timeFormatOption, implode('", "', self::$formats), $this->getOption('time_format')));
      }
    }
  }

  /**
   * Transforms a normalized date into a localized date string/array.
   *
   * @param  number $value  Normalized date.
   * @return string|array   Localized date string/array.
   */
  public function fromDateTime(\DateTime $dateTime)
  {
    $inputTimezone = $this->getOption('input_timezone');

    // convert time to UTC before passing it to the formatter
    if ($inputTimezone != 'UTC')
    {
      $dateTime->setTimezone(new \DateTimeZone('UTC'));
    }

    $value = $this->getIntlDateFormatter()->format((int)$dateTime->format('U'));

    if (intl_get_error_code() != 0)
    {
      throw new TransformationFailedException(intl_get_error_message());
    }

    return $value;
  }

  /**
   * Transforms a localized date string/array into a normalized date.
   *
   * @param string|array $value Localized date string/array
   * @return string Normalized date
   */
  public function toDateTime($value)
  {
    $inputTimezone = $this->getOption('input_timezone');

    if (!is_string($value))
    {
      throw new \InvalidArgumentException(sprintf('Expected argument of type string, %s given', gettype($value)));
    }

    $timestamp = $this->getIntlDateFormatter()->parse($value);

    if (intl_get_error_code() != 0)
    {
      throw new TransformationFailedException(intl_get_error_message());
    }

    // read timestamp into DateTime object - the formatter delivers in UTC
    $dateTime = new \DateTime(sprintf('@%s UTC', $timestamp));

    if ($inputTimezone != 'UTC')
    {
      $dateTime->setTimezone(new \DateTimeZone($inputTimezone));
    }

    return $dateTime;
  }

  /**
   * Returns a preconfigured IntlDateFormatter instance
   *
   * @return \IntlDateFormatter
   */
  protected function getIntlDateFormatter()
  {
    $locale = $this->getOption('locale');
    $dateFormat = $this->getIntlFormatConstant($this->dateFormatOption ? $this->getOption($this->dateFormatOption) : self::NONE);
    $timeFormat = $this->getIntlFormatConstant($this->timeFormatOption ? $this->getOption($this->timeFormatOption) : self::NONE);
    $timezone = $this->getOption('output_timezone');

    return new \IntlDateFormatter($locale, $dateFormat, $timeFormat, $timezone);
  }
}