<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\ConstraintValidatorInterface;
use Symfony\Components\Validator\Validators\Exception\UnknownOptionsException;
use Symfony\Components\Validator\Validators\Exception\MissingOptionsException;

abstract class ConstraintValidator implements ConstraintValidatorInterface
{
  private $options = array();
  private $knownOptions = array();
  private $requiredOptions = array();

  private $messageTemplate = '';
  private $messageParameters = array();

  public function initialize(array $options)
  {
    $this->options = $options;
    $this->knownOptions = array();
    $this->requiredOptions = array();

    $this->configure();

    $unknown = array_diff_key($this->options, $this->knownOptions);

    if (count($unknown) > 0)
    {
      throw new UnknownOptionsException($this, array_keys($unknown));
    }

    $missing = array_diff_key($this->requiredOptions, $this->options);

    if (count($missing) > 0)
    {
      throw new MissingOptionsException($this, array_keys($missing));
    }
  }

  protected function configure()
  {
  }

  protected function addOption($name, $default = null)
  {
    if (!array_key_exists($name, $this->options))
    {
      $this->options[$name] = $default;
    }

    $this->knownOptions[$name] = true;
  }

  protected function addRequiredOption($name)
  {
    $this->requiredOptions[$name] = true;
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