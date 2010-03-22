<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\ConstraintValidatorInterface;
use Symfony\Components\Validator\Validators\Exception\UnknownOptionsException;
use Symfony\Components\Validator\Validators\Exception\MissingOptionsException;

abstract class ConstraintValidator implements ConstraintValidatorInterface
{
  private $messageTemplate = '';
  private $messageParameters = array();

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