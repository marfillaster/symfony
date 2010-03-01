<?php

namespace Symfony\Components\Validator\Validators;

use Symfony\Components\Validator\ConstraintValidatorInterface;
use Symfony\Components\Validator\Validators\Exception\UnknownOptionsException;
use Symfony\Components\Validator\Validators\Exception\MissingOptionsException;

abstract class ConstraintValidator implements ConstraintValidatorInterface
{
  private $options = array();
  private $requiredOptions = array();

  private $messageTemplate = '';
  private $messageParameters = array();

  public function initialize(array $options)
  {
    $this->options = array();
    $this->requiredOptions = array();

    $this->configure();

    $unknown = array();

    foreach ($options as $key => $option)
    {
      if (!array_key_exists($key, $this->options))
      {
        $unknown[] = $key;
      }

      $this->options[$key] = $option;
    }

    if (count($unknown) > 0)
    {
      throw new UnknownOptionsException($this, $unknown);
    }

    $missing = array_diff($this->requiredOptions, array_keys($this->options));

    if (count($missing) > 0)
    {
      throw new MissingOptionsException($this, $missing);
    }
  }

  protected function configure()
  {
  }

  protected function addOption($name, $default = null)
  {
    $this->options[$name] = $default;
  }

  protected function addRequiredOption($name)
  {
    $this->requiredOptions[] = $name;
  }

  protected function getOption($name)
  {
    return $this->options[$name];
  }

  public function getMessageTemplate()
  {
    return $this->messageTemplate;
  }

  public function getMessageParameters()
  {
    return $this->messageParameters;
  }

  protected function setMessage($template, array $parameters = array())
  {
    $this->messageTemplate = $template;
    $this->messageParameters = $parameters;
  }
}