<?php

namespace Symfony\Components\Form\ValueTransformer;

/**
 * BaseValueTransformer is the base class for all value transformers.
 */
abstract class BaseValueTransformer implements ValueTransformerInterface
{
  protected
    $options         = array(),
    $knownOptions    = array(),
    $requiredOptions = array();

  /**
   * Constructor.
   *
   * @param array $options     An array of options
   *
   * @throws \InvalidArgumentException when a option is not supported
   * @throws \RuntimeException         when a required option is not given
   */
  public function __construct(array $options = array())
  {
    $this->options = $options;

    $this->configure();

    // check option names
    if ($diff = array_diff_key($this->options, $this->knownOptions))
    {
      throw new \InvalidArgumentException(sprintf('%s does not support the following options: \'%s\'.', get_class($this), implode('\', \'', array_keys($diff))));
    }

    // check required options
    if ($diff = array_diff_key($this->requiredOptions, $this->options))
    {
      throw new \RuntimeException(sprintf('%s requires the following options: \'%s\'.', get_class($this), implode('\', \'', array_keys($diff))));
    }
  }

  /**
   * Configures the current value transformer.
   */
  protected function configure()
  {
  }

  /**
   * Gets an option value.
   *
   * @param  string $name  The option name
    *
   * @return mixed  The option value
   */
  protected function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
  }

  /**
   * Adds a new option value with a default value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The default value
   *
   * @return BaseValueTransformer The current value transformer instance
   */
  protected function addOption($name, $value = null)
  {
    $this->knownOptions[$name] = true;

    if (!array_key_exists($name, $this->options))
    {
      $this->options[$name] = $value;
    }

    return $this;
  }

  /**
   * Changes an option value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The value
   *
   * @return BaseValueTransformer The current value transformer instance
   *
   * @throws \InvalidArgumentException when a option is not supported
   */
  protected function setOption($name, $value)
  {
    if (!in_array($name, array_merge(array_keys($this->options), $this->requiredOptions)))
    {
      throw new \InvalidArgumentException(sprintf('%s does not support the following option: \'%s\'.', get_class($this), $name));
    }

    $this->options[$name] = $value;

    return $this;
  }

  /**
   * Returns true if the option exists.
   *
   * @param  string $name  The option name
   *
   * @return bool true if the option exists, false otherwise
   */
  protected function hasOption($name)
  {
    return isset($this->options[$name]);
  }

  /**
   * Adds a required option.
   *
   * @param string $name  The option name
   *
   * @return BaseValueTransformer The current value transformer instance
   */
  protected function addRequiredOption($name)
  {
    $this->knownOptions[$name] = true;
    $this->requiredOptions[$name] = true;

    return $this;
  }
}